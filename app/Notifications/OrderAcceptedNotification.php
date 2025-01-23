<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use App\Models\Order;

class OrderAcceptedNotification extends Notification
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
        $message = "🎉 Order Update #" . $this->order->id . "\n\n";
        $message .= "Your order has been accepted by " . $this->order->provider->name . "!\n";
        $message .= "Status: Processing\n\n";
        $message .= "Services:\n";
        if ($this->order->washing) $message .= "- Washing\n";
        if ($this->order->ironing) $message .= "- Ironing\n";
        if ($this->order->dry_cleaning) $message .= "- Dry Cleaning\n\n";
        $message .= "Pickup: " . $this->order->pickup_time->format('d M Y h:i A') . "\n";
        $message .= "Delivery: " . $this->order->delivery_time->format('d M Y h:i A') . "\n";
        $message .= "Total: RM " . number_format($this->order->total, 2);

        return TelegramMessage::create()
            ->content($message);
    }
} 