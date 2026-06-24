<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class SkuService
{
    public function generateCategoryPrefix(string $name, ?int $ignoreCategoryId = null): string
    {
        $base = Str::upper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $name) ?: 'CAT', 0, 4));

        return $this->ensureUniqueCategoryPrefix($base, $ignoreCategoryId);
    }

    public function ensureUniqueCategoryPrefix(string $prefix, ?int $ignoreCategoryId = null): string
    {
        $normalized = Str::upper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $prefix) ?: 'CAT', 0, 12));
        $candidate = $normalized;
        $counter = 2;

        while (
            Category::query()
                ->when($ignoreCategoryId, fn ($query) => $query->where('id', '!=', $ignoreCategoryId))
                ->where('sku_prefix', $candidate)
                ->exists()
        ) {
            $suffix = (string) $counter;
            $candidate = Str::substr($normalized, 0, max(1, 12 - strlen($suffix))) . $suffix;
            $counter++;
        }

        return $candidate;
    }

    public function generateProductSku(?Category $category): string
    {
        // Use category prefix if available, otherwise use product_prefix setting or default to 'GEN'
        $prefix = $category?->sku_prefix ?: (AppSetting::current()->product_prefix ?: 'GEN');
        $maxSequence = 0;

        Product::query()
            ->where('sku', 'like', $prefix . '-%')
            ->pluck('sku')
            ->each(function (string $sku) use (&$maxSequence, $prefix) {
                if (preg_match('/^' . preg_quote($prefix, '/') . '-(\d+)$/', $sku, $matches) === 1) {
                    $maxSequence = max($maxSequence, (int) $matches[1]);
                }
            });

        return sprintf('%s-%04d', $prefix, $maxSequence + 1);
    }
}
