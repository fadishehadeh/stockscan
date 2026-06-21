<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 12px;
            margin: 24px;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .header {
            margin-bottom: 24px;
        }

        .kicker {
            color: #0f6cbd;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .subtitle,
        .meta {
            color: #64748b;
            line-height: 1.5;
        }

        .meta {
            margin-top: 8px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #dbe4ee;
            background: #f8fafc;
            padding: 12px;
            vertical-align: top;
        }

        .summary-label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #64748b;
            margin-bottom: 10px;
        }

        .summary-value {
            display: block;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .section {
            margin-top: 24px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
        }

        table.report th,
        table.report td {
            border: 1px solid #dbe4ee;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }

        table.report th {
            background: #f1f5f9;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #475569;
        }

        .muted {
            color: #64748b;
        }

        .empty {
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            color: #64748b;
            padding: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="kicker">Reporting</p>
        <h1 class="title">Inventory Report</h1>
        <p class="subtitle">Filtered inventory, low-stock exposure, value, and movement summary.</p>
        <p class="meta">
            Generated: {{ $generatedAt->format('d M Y H:i') }}<br>
            Date range: {{ $filters['from'] ?: 'Any' }} to {{ $filters['to'] ?: 'Any' }}<br>
            Category: {{ $categories->firstWhere('id', $filters['categoryId'])?->name ?? 'All categories' }}<br>
            Movement type: {{ $filters['type'] ? ucfirst($filters['type']) : 'All movement types' }}
        </p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="summary-label">Inventory Items</span>
                <span class="summary-value">{{ $inventoryItems->count() }}</span>
                <span class="muted">Filtered results</span>
            </td>
            <td>
                <span class="summary-label">Low Stock</span>
                <span class="summary-value">{{ $lowStockItems->count() }}</span>
                <span class="muted">Need attention</span>
            </td>
            <td>
                <span class="summary-label">Stock Out Qty</span>
                <span class="summary-value">{{ $movementTotals['out'] }}</span>
                <span class="muted">Movement total</span>
            </td>
            <td>
                <span class="summary-label">Inventory Value</span>
                <span class="summary-value">${{ number_format($inventoryValue, 2) }}</span>
                <span class="muted">At current cost</span>
            </td>
        </tr>
    </table>

    <div class="section">
        <h2 class="section-title">Current Inventory</h2>
        <table class="report">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Cost</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventoryItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category?->name ?? 'No category' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->cost, 2) }}</td>
                        <td>${{ number_format($item->quantity * $item->cost, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Low Stock Report</h2>
        @if ($lowStockItems->isEmpty())
            <div class="empty">No low-stock items for this filter set.</div>
        @else
            <table class="report">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Qty Left</th>
                        <th>Min Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lowStockItems as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->min_stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title">Recent Movement Report</h2>
        @if ($movements->isEmpty())
            <div class="empty">No movement records found.</div>
        @else
            <table class="report">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $movement->product->name }}</td>
                            <td>{{ ucfirst($movement->type) }}</td>
                            <td>{{ $movement->quantity }}</td>
                            <td>{{ $movement->user->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
