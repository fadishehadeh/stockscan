<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Product;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeService
{
    public function generateUniqueCode(?AppSetting $settings = null): string
    {
        $settings ??= AppSetting::current();
        $prefix = $settings->barcode_prefix ?? '';
        $length = max(6, (int) $settings->barcode_random_length);

        for ($attempt = 0; $attempt < 25; $attempt++) {
            $suffix = '';

            for ($i = 0; $i < $length; $i++) {
                $suffix .= (string) random_int(0, 9);
            }

            $barcode = $prefix . $suffix;

            if (! Product::query()->where('barcode', $barcode)->exists()) {
                return $barcode;
            }
        }

        do {
            $barcode = $prefix . now()->format('ymdHis') . Str::padLeft((string) random_int(0, 999), 3, '0');
        } while (Product::query()->where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function svg(string $barcode, int $scale = 2, int $height = 50): string
    {
        $generator = new BarcodeGeneratorSVG();

        return $generator->getBarcode($barcode, $generator::TYPE_CODE_128, $scale, $height);
    }
}
