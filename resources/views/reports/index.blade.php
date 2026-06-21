@extends('layouts.app', ['title' => 'Reports - StockScan', 'heading' => 'Reports'])

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div class="max-w-2xl">
                <p class="eyebrow">Reporting</p>
                <h3 class="panel-title mt-2">Inventory reports</h3>
                <p class="panel-subtitle">Filter inventory, low-stock exposure, value, and stock movement from one place.</p>
            </div>
            <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-secondary">Export PDF</a>
        </div>

        <form method="GET" class="filter-bar mt-6 grid gap-4 lg:grid-cols-5">
            <input type="date" name="from" value="{{ $filters['from'] }}" class="input">
            <input type="date" name="to" value="{{ $filters['to'] }}" class="input">
            <select name="category" class="input">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($filters['categoryId'] === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="type" class="input">
                <option value="">All movement types</option>
                <option value="in" @selected($filters['type'] === 'in')>Stock In</option>
                <option value="out" @selected($filters['type'] === 'out')>Stock Out</option>
                <option value="adjustment" @selected($filters['type'] === 'adjustment')>Adjustment</option>
            </select>
            <button class="btn btn-secondary">Apply Filters</button>
        </form>
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="metric-card metric-card-highlight">
            <p class="metric-label">Inventory Items</p>
            <p class="metric-value">{{ $inventoryItems->count() }}</p>
            <p class="metric-meta">Filtered results</p>
        </article>
        <article class="metric-card">
            <p class="metric-label">Low Stock</p>
            <p class="metric-value">{{ $lowStockItems->count() }}</p>
            <p class="metric-meta">Need attention</p>
        </article>
        <article class="metric-card">
            <p class="metric-label">Stock Out Qty</p>
            <p class="metric-value">{{ $movementTotals['out'] }}</p>
            <p class="metric-meta">Movement total</p>
        </article>
        <article class="metric-card">
            <p class="metric-label">Inventory Value</p>
            <p class="metric-value">${{ number_format($inventoryValue, 2) }}</p>
            <p class="metric-meta">At current cost</p>
        </article>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Inventory</p>
                    <h3 class="panel-title mt-2">Current inventory</h3>
                </div>
            </div>
            <div class="table-shell mt-5">
                <table class="min-w-full divide-y divide-slate-200/80 bg-white text-sm">
                    <thead class="table-head">
                        <tr>
                            <th class="px-4 py-4">Product</th>
                            <th class="px-4 py-4">Qty</th>
                            <th class="px-4 py-4">Cost</th>
                            <th class="px-4 py-4">Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80">
                        @foreach ($inventoryItems as $item)
                            <tr class="table-row">
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-slate-950">{{ $item->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $item->category?->name ?? 'No category' }}</p>
                                </td>
                                <td class="px-4 py-4">{{ $item->quantity }}</td>
                                <td class="px-4 py-4">${{ number_format($item->cost, 2) }}</td>
                                <td class="px-4 py-4">${{ number_format($item->quantity * $item->cost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Risk</p>
                    <h3 class="panel-title mt-2">Low stock report</h3>
                </div>
            </div>
            <div class="surface-list mt-5">
                @forelse ($lowStockItems as $item)
                    <div class="surface-item-strong">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-950">{{ $item->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $item->sku }}</p>
                            </div>
                            <div class="text-right text-sm">
                                <p class="font-semibold text-amber-800">{{ $item->quantity }} left</p>
                                <p class="text-amber-700">Min {{ $item->min_stock }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No low-stock items for this filter set.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="panel mt-6">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Movements</p>
                <h3 class="panel-title mt-2">Recent movement report</h3>
            </div>
        </div>
        <div class="table-shell mt-5">
            <table class="min-w-full divide-y divide-slate-200/80 bg-white text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-4 py-4">Time</th>
                        <th class="px-4 py-4">Product</th>
                        <th class="px-4 py-4">Type</th>
                        <th class="px-4 py-4">Qty</th>
                        <th class="px-4 py-4">User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80">
                    @forelse ($movements as $movement)
                        <tr class="table-row">
                            <td class="px-4 py-4 table-cell-muted">{{ $movement->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-4">{{ $movement->product->name }}</td>
                            <td class="px-4 py-4">{{ ucfirst($movement->type) }}</td>
                            <td class="px-4 py-4">{{ $movement->quantity }}</td>
                            <td class="px-4 py-4">{{ $movement->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10">
                                <div class="empty-state">No movement records found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
