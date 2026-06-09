<?php

namespace App\Notifications;

use App\Models\LowStockAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlertNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly LowStockAlert $alert)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->alert->product;

        return (new MailMessage())
            ->subject('Low stock alert: ' . $product->name)
            ->greeting('Low stock alert')
            ->line($product->name . ' has reached low stock.')
            ->line('Current quantity: ' . $product->quantity)
            ->line('Minimum threshold: ' . $product->min_stock)
            ->line('SKU: ' . $product->sku)
            ->line('Please restock this product as needed.');
    }
}
