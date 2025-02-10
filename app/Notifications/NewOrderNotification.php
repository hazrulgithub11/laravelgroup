<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
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
        return [TelegramChannel::class];
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

        return TelegramMessage::create()
            ->to($notifiable->telegram_chat_id)
            ->content(
                "🔔 *New Order #" . $this->order->id . "*\n\n" .
                "📦 Services: " . implode(', ', $services) . "\n" .
                "📅 Pickup: " . $this->order->pickup_time->format('d M Y h:i A') . "\n" .
                "🚚 Delivery: " . $this->order->delivery_time->format('d M Y h:i A') . "\n" .
                "💰 Total: RM " . number_format($this->order->total, 2) . "\n" .
                "📍 Address: " . $this->order->address . "\n\n" .
                "Please use the buttons below to accept or cancel this order."
            )
            ->options([
                'reply_markup' => json_encode([
                    'inline_keyboard' => [[
                        ['text' => '✅ Accept Order #' . $this->order->id, 'callback_data' => 'accept_order_' . $this->order->id],
                        ['text' => '❌ Cancel Order #' . $this->order->id, 'callback_data' => 'cancel_order_' . $this->order->id]
                    ]]
                ])
            ]);
    }
} 