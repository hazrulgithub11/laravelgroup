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

            if (isset($update['message'])) {
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

    public function setWebhook()
    {
        // For local testing, we'll use ngrok or a similar service
        // You need to run ngrok http 80 and use the HTTPS URL it provides
        $webhookUrl = 'https://your-ngrok-url.ngrok.io/api/telegram/webhook';
        // Or use a service like webhook.site for testing
        // $webhookUrl = 'https://webhook.site/your-unique-id';
        
        Log::info('Setting Telegram webhook:', ['url' => $webhookUrl]);
        
        // First, delete any existing webhook
        $deleteResponse = Http::get('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/deleteWebhook');
        Log::info('Delete webhook response:', $deleteResponse->json());

        // Set the new webhook
        $response = Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/setWebhook', [
            'url' => $webhookUrl,
            'allowed_updates' => ['message']
        ]);

        $result = $response->json();
        Log::info('Webhook setup response:', $result);

        if (!$response->successful() || !$result['ok']) {
            Log::error('Failed to set webhook:', $result);
            return response()->json([
                'success' => false,
                'message' => 'Failed to set webhook',
                'response' => $result
            ], 400);
        }

        // Get current webhook info
        $info = Http::get('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/getWebhookInfo')->json();
        
        return response()->json([
            'success' => true,
            'message' => 'Webhook set successfully',
            'webhook_url' => $webhookUrl,
            'webhook_info' => $info
        ]);
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
} 