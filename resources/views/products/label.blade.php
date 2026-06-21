<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Sticker - {{ $product->name }}</title>
    <style>
        :root {
            color-scheme: light;
            --page-width: {{ $labelConfig['page_width_mm'] }}mm;
            --page-height: {{ $labelConfig['page_height_mm'] }}mm;
            --label-width: {{ $labelConfig['width'] }}px;
            --label-padding: {{ $labelConfig['padding'] }}px;
        }

        @page {
            size: var(--page-width) var(--page-height);
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #0f172a;
            background: #f8fafc;
        }

        .screen-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 24px;
            border-bottom: 1px solid #dbe4ee;
            background: #ffffff;
        }

        .screen-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .screen-copy {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .toolbar-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
        }

        .toolbar-button-primary {
            border-color: #0f6cbd;
            background: #0f6cbd;
            color: #ffffff;
        }

        .toolbar-note {
            width: 100%;
            font-size: 13px;
            color: #64748b;
        }

        .toolbar-note strong {
            color: #0f172a;
        }

        .print-stage {
            display: flex;
            justify-content: center;
            padding: 28px;
        }

        .label-sheet {
            width: var(--label-width);
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            padding: 16px;
            background: #ffffff;
        }

        .label {
            width: 100%;
            border: 1px solid #d4d4d8;
            border-radius: 16px;
            padding: var(--label-padding);
            background: #ffffff;
        }

        .name {
            font-size: {{ $labelConfig['font'] }}px;
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 8px;
        }

        .meta {
            color: #52525b;
            font-size: {{ $labelConfig['meta'] }}px;
            margin-bottom: 12px;
        }

        .barcode-box {
            overflow: hidden;
        }

        .barcode-text {
            margin-top: 10px;
            font-size: {{ max(12, $labelConfig['meta']) }}px;
            color: #334155;
            letter-spacing: 0.08em;
        }

        .price {
            font-size: {{ $labelConfig['price'] ?: 28 }}px;
            font-weight: 700;
            margin-top: 14px;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .screen-toolbar {
                display: none;
            }

            .print-stage {
                padding: 0;
                display: block;
            }

            .label-sheet {
                width: 100%;
                border: 0;
                border-radius: 0;
                padding: 0;
            }

            .label {
                border: 0;
                border-radius: 0;
                width: 100%;
                min-height: var(--page-height);
            }
        }
    </style>
</head>
<body @if ($autoprint) onload="window.print()" @endif>
    <div class="screen-toolbar">
        <div>
            <h1 class="screen-title">Print barcode sticker</h1>
            <p class="screen-copy">{{ $product->name }} · {{ $labelConfig['name'] }} label</p>
        </div>

        <div class="toolbar-actions">
            <a href="{{ route('products.label', ['product' => $product, 'size' => 'small']) }}" class="toolbar-button">Small</a>
            <a href="{{ route('products.label', ['product' => $product, 'size' => 'medium']) }}" class="toolbar-button">Medium</a>
            <a href="{{ route('products.label', ['product' => $product, 'size' => 'large']) }}" class="toolbar-button">Large</a>
            <button type="button" class="toolbar-button toolbar-button-primary" onclick="window.print()">Print Now</button>
        </div>

        <div class="toolbar-note">
            Choose the <strong>local sticker printer</strong> in the browser print dialog and match the paper size to <strong>{{ $labelConfig['name'] }}</strong>.
        </div>
    </div>

    <div class="print-stage">
        <div class="label-sheet">
            <div class="label">
                <div class="name">{{ $product->name }}</div>
                <div class="meta">{{ $product->sku }} · {{ $product->category?->name ?? 'No category' }}</div>
                <div class="barcode-box">{!! $barcodeSvg !!}</div>
                <div class="barcode-text">{{ $product->barcode }}</div>
                @if ($product->selling_price && $labelConfig['price'] > 0)
                    <div class="price">${{ number_format($product->selling_price, 2) }}</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
