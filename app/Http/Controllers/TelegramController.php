<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Provider;
use App\Models\Order;
use App\Notifications\OrderAcceptedNotification;
use App\Notifications\OrderCancelledNotification;
use Illuminate\Support\Facades\DB;

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
        try {
            $update = $request->all();
            Log::info('Telegram Update Received:', ['update' => $update]);

            if (isset($update['callback_query'])) {
                $callbackQuery = $update['callback_query'];
                $data = $callbackQuery['data'];

                if (preg_match('/(accept|cancel)_order_(\d+)/', $data, $matches)) {
                    $action = $matches[1];
                    $orderId = $matches[2];

                    // Find the order
                    $order = Order::find($orderId);
                    
                    if (!$order) {
                        Log::error('Order not found:', ['order_id' => $orderId]);
                        return response()->json(['success' => false, 'error' => 'Order not found']);
                    }

                    // Update the status
                    $status = ($action === 'accept') ? 'processing' : 'cancelled';
                    $order->status = $status;
                    $order->save();

                    Log::info('Order status updated:', [
                        'order_id' => $orderId,
                        'old_status' => $order->getOriginal('status'),
                        'new_status' => $status
                    ]);

                    // Update Telegram message
                    $newText = ($action === 'accept') 
                        ? "✅ Order #{$orderId} has been accepted!"
                        : "❌ Order #{$orderId} has been cancelled.";

                    Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/editMessageText', [
                        'chat_id' => $callbackQuery['message']['chat']['id'],
                        'message_id' => $callbackQuery['message']['message_id'],
                        'text' => $newText
                    ]);

                    return response()->json(['success' => true]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Webhook error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
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

            $message = " *Test Notification*\n\n";
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