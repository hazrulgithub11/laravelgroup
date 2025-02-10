<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Provider;

class TelegramController extends Controller
{
    public function setWebhook()
    {
        $webhookUrl = url('/api/telegram/webhook');
        Log::info('Setting webhook to: ' . $webhookUrl);

        $response = Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/setWebhook', [
            'url' => $webhookUrl,
            'allowed_updates' => ['message', 'callback_query']
        ]);

        Log::info('Webhook setup response:', $response->json());

        return response()->json([
            'webhook_url' => $webhookUrl,
            'telegram_response' => $response->json()
        ]);
    }

    public function webhook(Request $request)
    {
        Log::info('Telegram webhook payload:', $request->all());

        $update = $request->all();

        if (isset($update['message'])) {
            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';
            $username = $message['chat']['username'] ?? null;

            if ($text === '/start' && $username) {
                // Remove @ from username if present
                $username = ltrim($username, '@');

                // Try to find and update user
                $user = User::where('telegram_username', $username)->first();
                if ($user) {
                    $user->update([
                        'telegram_chat_id' => $chatId,
                        'telegram_verified_at' => now()
                    ]);
                    Log::info("Updated user {$user->name} with chat ID: $chatId");
                }

                // Try to find and update provider
                $provider = Provider::where('telegram_username', $username)->first();
                if ($provider) {
                    $provider->update([
                        'telegram_chat_id' => $chatId
                    ]);
                    Log::info("Updated provider {$provider->name} with chat ID: $chatId");
                }

                // Send welcome message
                $response = "Welcome to the Laundry System Bot! 🧺\n\n";
                $response .= "Your Chat ID is: `$chatId`\n";
                $response .= "Your Username is: @$username\n\n";
                
                if ($user || $provider) {
                    $response .= "✅ Your Telegram information has been automatically saved.";
                } else {
                    $response .= "❗ No matching user or provider found for @$username";
                }

                Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/sendMessage', [
                    'chat_id' => $chatId,
                    'text' => $response,
                    'parse_mode' => 'Markdown'
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function sendTestNotification(Request $request)
    {
        if (!$request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'JSON response expected'
            ]);
        }

        try {
            $user = auth()->user();
            
            if (empty($user->telegram_chat_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Telegram chat ID not found. Please connect your Telegram first.'
                ]);
            }

            $message = "🔔 *Test Notification*\n\n";
            $message .= "This is a test notification from your Laundry System.\n";
            $message .= "If you received this message, your Telegram notifications are working correctly! 🎉";

            $response = Http::withoutVerifying()
                ->post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/sendMessage', [
                    'chat_id' => $user->telegram_chat_id,
                    'text' => $message,
                    'parse_mode' => 'Markdown'
                ]);

            Log::info('Telegram test notification response:', $response->json());

            if ($response->successful() && ($response->json()['ok'] ?? false)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . ($response->json()['description'] ?? 'Unknown error')
            ]);

        } catch (\Exception $e) {
            Log::error('Telegram test notification error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}