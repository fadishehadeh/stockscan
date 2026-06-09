<?php

namespace App\Services;

use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockAlertNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class LowStockAlertService
{
    public function __construct(private readonly ActivityLogService $activityLogService)
    {
    }

    public function sync(Product $product, bool $wasLowStock, ?User $actor = null): void
    {
        $isLowStock = $product->isLowStock();
        $activeAlert = LowStockAlert::query()
            ->where('product_id', $product->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (! $wasLowStock && $isLowStock && ! $activeAlert) {
            $alert = LowStockAlert::query()->create([
                'product_id' => $product->id,
                'created_by_user_id' => $actor?->id,
                'status' => 'active',
                'threshold' => $product->min_stock,
                'quantity_when_triggered' => $product->quantity,
                'notified_at' => now(),
            ]);

            $this->activityLogService->record(
                action: 'low_stock.alert_created',
                message: 'Low stock alert created for ' . $product->name . '.',
                user: $actor,
                entity: $product,
                metadata: ['quantity' => $product->quantity, 'threshold' => $product->min_stock]
            );

            $this->sendEmailNotifications($alert);

            return;
        }

        if ($wasLowStock && ! $isLowStock && $activeAlert) {
            $activeAlert->update([
                'status' => 'resolved',
                'resolved_by_user_id' => $actor?->id,
                'resolved_at' => now(),
            ]);

            $this->activityLogService->record(
                action: 'low_stock.alert_resolved',
                message: 'Low stock alert resolved for ' . $product->name . '.',
                user: $actor,
                entity: $product,
                metadata: ['quantity' => $product->quantity, 'threshold' => $product->min_stock]
            );
        }
    }

    private function sendEmailNotifications(LowStockAlert $alert): void
    {
        $recipients = User::query()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        try {
            Notification::send($recipients, new LowStockAlertNotification($alert->load('product')));
        } catch (Throwable $exception) {
            Log::warning('Unable to send low stock email notifications.', [
                'alert_id' => $alert->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
