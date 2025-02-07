<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use App\Models\Order;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        if (!$notifiable->telegram_chat_id) {
            throw new \Exception('Provider has not set up their Telegram Chat ID');
        }

        $services = [];
        if ($this->order->washing) $services[] = 'Washing';
        if ($this->order->ironing) $services[] = 'Ironing';
        if ($this->order->dry_cleaning) $services[] = 'Dry Cleaning';

        $message = "🔔 *New Order #" . $this->order->id . "*\n\n";
        $message .= "📦 Services: " . implode(', ', $services) . "\n";
        $message .= "📅 Pickup: " . $this->order->pickup_time->format('d M Y h:i A') . "\n";
        $message .= "🚚 Delivery: " . $this->order->delivery_time->format('d M Y h:i A') . "\n";
        $message .= "💰 Total: RM " . number_format($this->order->total, 2) . "\n";
        $message .= "📍 Address: " . $this->order->address . "\n\n";
        $message .= "Please login to your dashboard to accept this order.";

        $response = Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/sendMessage', [
            'chat_id' => $notifiable->telegram_chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'View Order',
                            'url' => url('/provider/dashboard')
                        ]
                    ]
                ]
            ])
        ]);

        if (!$response->successful() || !$response->json('ok')) {
            \Log::error('Telegram API Error:', [
                'response' => $response->json(),
                'provider_id' => $notifiable->id,
                'telegram_chat_id' => $notifiable->telegram_chat_id
            ]);
            throw new \Exception('Failed to send Telegram notification: ' . ($response->json('description') ?? 'Unknown error'));
        }

        return $response->json();
    }
} 