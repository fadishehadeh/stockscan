<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'scanner_mode',
    'auto_submit_on_enter',
    'default_post_scan_behavior',
    'default_stock_action',
    'label_size_default',
    'barcode_prefix',
    'barcode_random_length',
    'product_prefix',
])]
class AppSetting extends Model
{
    protected function casts(): array
    {
        return [
            'auto_submit_on_enter' => 'boolean',
            'barcode_random_length' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'scanner_mode' => 'keyboard_wedge',
            'auto_submit_on_enter' => true,
            'default_post_scan_behavior' => 'open_product_actions',
            'default_stock_action' => 'out',
            'label_size_default' => 'medium',
            'barcode_prefix' => null,
            'barcode_random_length' => 10,
            'product_prefix' => null,
        ]);
    }
}
