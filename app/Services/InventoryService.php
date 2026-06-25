<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function __construct(
        private readonly LowStockAlertService $lowStockAlertService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function record(
        Product $product,
        string $type,
        int $quantity,
        User $user,
        ?float $unitCost = null,
        ?string $note = null,
        array $metadata = [],
    ): StockTransaction {
        if ($type !== 'adjustment' && $quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be at least 1.',
            ]);
        }

        if ($type === 'adjustment' && $quantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Adjustment quantity cannot be negative.',
            ]);
        }

        return DB::transaction(function () use ($product, $type, $quantity, $user, $unitCost, $note, $metadata) {
            $product->refresh();
            $wasLowStock = $product->isLowStock();

            $before = (int) $product->quantity;
            $after = match ($type) {
                'in' => $before + $quantity,
                'out' => $before - $quantity,
                'adjustment' => $quantity,
            };

            if ($after < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stock-out cannot reduce inventory below zero.',
                ]);
            }

            $product->quantity = $after;

            if ($type === 'in' && $unitCost !== null) {
                $product->cost = $unitCost;
            }

            $product->save();

            $transaction = StockTransaction::query()->create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => $type,
                'quantity' => $quantity,
                'unit_cost' => $type === 'in' ? ($unitCost ?? $product->cost) : null,
                'note' => $note,
                'quantity_before' => $before,
                'quantity_after' => $after,
            ]);

            $this->activityLogService->record(
                action: 'stock.' . $type,
                message: ucfirst($type) . ' stock recorded for ' . $product->name . '.',
                user: $user,
                entity: $product,
                metadata: array_merge([
                    'quantity' => $quantity,
                    'quantity_before' => $before,
                    'quantity_after' => $after,
                ], $metadata)
            );

            $this->lowStockAlertService->sync($product, $wasLowStock, $user);

            return $transaction;
        });
    }
}
