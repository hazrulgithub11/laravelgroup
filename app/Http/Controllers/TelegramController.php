<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    private function sendTelegramMessage($chatId, $message)
    {
        return Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }

    public function webhook(Request $request)
    {
        try {
            $update = $request->all();
            Log::info('Telegram Webhook Received:', [
                'update' => $update,
                'headers' => $request->headers->all()
            ]);

            // Handle callback queries (button clicks)
            if (isset($update['callback_query'])) {
                $callbackQuery = $update['callback_query'];
                $chatId = $callbackQuery['message']['chat']['id'];
                $messageId = $callbackQuery['message']['message_id'];
                $data = $callbackQuery['data'];

                // Log the button click
                Log::info('Button clicked:', [
                    'chat_id' => $chatId,
                    'button' => $data
                ]);

                // Handle different button clicks
                switch ($data) {
                    case 'hello_back':
                        $newText = "👋 Hello back from Telegram!";
                        break;
                        
                    case 'bye':
                        $newText = "👋 Goodbye from Telegram!";
                        break;
                        
                    default:
                        $newText = "Unknown button clicked";
                }
                
                // Update the original message
                $response = Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/editMessageText', [
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => $newText
                ]);

                Log::info('Message updated:', $response->json());

                // Answer callback query to remove loading state
                Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/answerCallbackQuery', [
                    'callback_query_id' => $callbackQuery['id']
                ]);
            }
            // Handle regular messages (including /start)
            else if (isset($update['message'])) {
                $message = $update['message'];
                $chatId = $message['chat']['id'];
                $text = $message['text'] ?? '';

                Log::info('Processing message:', [
                    'chat_id' => $chatId,
                    'text' => $text
                ]);

                if ($text === '/start') {
                    $response = "Welcome to the Laundry System Bot! 🧺\n\n";
                    $response .= "Your Chat ID is: `" . $chatId . "`\n\n";
                    $response .= "Please save this Chat ID and update it in your provider profile to receive order notifications.";
                    
                    $result = $this->sendTelegramMessage($chatId, $response);
                    Log::info('Start message sent:', [
                        'response' => $result->json()
                    ]);
                }
            }

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            Log::error('Telegram Webhook Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    public function testMessage($chatId)
    {
        try {
            Log::info('Testing message to chat ID:', ['chat_id' => $chatId]);
            
            $message = "🔔 This is a test message from your Laundry System Bot.\n\n";
            $message .= "If you receive this message, the notification system is working correctly!";
            
            $response = $this->sendTelegramMessage($chatId, $message);
            $result = $response->json();
            
            Log::info('Test message response:', $result);
            
            return response()->json([
                'success' => $result['ok'] ?? false,
                'message' => $result['ok'] ? 'Message sent successfully' : 'Failed to send message',
                'response' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Test message error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendTestNotification(Request $request)
    {
        try {
            // First, let's try to get chat info
            $response = Http::withoutVerifying()
                ->get('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/getChat', [
                    'chat_id' => '5802892985'  // Your chat ID
                ]);

            Log::info('Get Chat Response:', $response->json());

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get chat info: ' . $response->body()
                ]);
            }

            $chatInfo = $response->json();
            
            if (!isset($chatInfo['ok']) || !$chatInfo['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not get chat info: ' . ($chatInfo['description'] ?? 'Unknown error')
                ]);
            }

            // If we got here, we have valid chat info
            $chatId = $chatInfo['result']['id'];
            
            // Now send a test message with buttons
            $response = Http::withoutVerifying()
                ->post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/sendMessage', [
                    'chat_id' => $chatId,
                    'text' => "👋 Hello from home!",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => '👋 Hello Back', 'callback_data' => 'hello_back'],
                                ['text' => '👋 Bye', 'callback_data' => 'bye']
                            ]
                        ]
                    ])
                ]);

            Log::info('Send Message Response:', $response->json());

            if ($response->successful() && ($response->json()['ok'] ?? false)) {
                return response()->json(['success' => true, 'message' => 'Test message sent successfully! Check your Telegram.']);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message: ' . ($response->json()['description'] ?? 'Unknown error')
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function setWebhook()
    {
        try {
            $webhookUrl = url('/api/telegram/webhook'); // Your webhook URL
            
            $response = Http::get('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/setWebhook', [
                'url' => $webhookUrl,
                'allowed_updates' => ['message', 'callback_query'] // Explicitly allow callback_query updates
            ]);

            Log::info('Webhook setup response:', $response->json());

            return response()->json([
                'success' => true,
                'message' => 'Webhook set: ' . $webhookUrl,
                'response' => $response->json()
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook setup error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error setting webhook: ' . $e->getMessage()
            ]);
        }
    }
} 