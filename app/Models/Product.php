<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'sku',
    'barcode',
    'category_id',
    'cost',
    'selling_price',
    'quantity',
    'min_stock',
    'description',
    'image_path',
])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'selling_price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class)->latest();
    }

    public function lowStockAlerts(): HasMany
    {
        return $this->hasMany(LowStockAlert::class)->latest();
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_stock;
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }
}
