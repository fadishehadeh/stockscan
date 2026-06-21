<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Label · {{ $product->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; background: #fff; }
        .label { width: {{ $labelConfig['width'] }}px; border: 1px solid #d4d4d8; border-radius: 16px; padding: {{ $labelConfig['padding'] }}px; }
        .name { font-size: {{ $labelConfig['font'] }}px; font-weight: 700; margin-bottom: 8px; }
        .meta { color: #52525b; font-size: {{ $labelConfig['meta'] }}px; margin-bottom: 14px; }
        .price { font-size: {{ $labelConfig['price'] ?: 28 }}px; font-weight: 700; margin-top: 14px; }
    </style>
</head>
<body onload="window.print()">
    <div class="label">
        <div class="name">{{ $product->name }}</div>
        <div class="meta">{{ $product->sku }} · {{ $product->category?->name ?? 'No category' }}</div>
        {!! $barcodeSvg !!}
        <div class="meta">{{ $product->barcode }}</div>
        @if ($product->selling_price && $labelConfig['price'] > 0)
            <div class="price">${{ number_format($product->selling_price, 2) }}</div>
        @endif
    </div>
</body>
</html>
