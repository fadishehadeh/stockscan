@extends('layouts.app', ['title' => 'Dashboard · StockScan', 'heading' => 'Dashboard'])

@section('content')
    <section class="panel-hero">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <p class="eyebrow">Operational Snapshot</p>
                <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Stock visibility for the day's work.</h3>
                <p class="mt-3 text-sm leading-6 text-slate-500">Track current inventory, spot low-stock pressure quickly, and move from dashboard to action screens without extra navigation.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 xl:min-w-[21rem]">
                <a href="{{ route('scan.index') }}" class="btn btn-primary">Open Scan Station</a>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Review Products</a>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        @foreach ([
            ['label' => 'Total Products', 'value' => $stats['total_products'], 'meta' => 'Tracked items', 'highlight' => true],
            ['label' => 'In Stock', 'value' => $stats['in_stock'], 'meta' => 'Available now'],
            ['label' => 'Low Stock', 'value' => $stats['low_stock'], 'meta' => 'Need attention'],
            ['label' => 'Out of Stock', 'value' => $stats['out_of_stock'], 'meta' => 'Unavailable items'],
            ['label' => 'Inventory Value', 'value' => '$' . number_format($stats['inventory_value'], 2), 'meta' => 'At current cost'],
        ] as $card)
            <article class="metric-card {{ !empty($card['highlight']) ? 'metric-card-highlight' : '' }}">
                <p class="metric-label">{{ $card['label'] }}</p>
                <p class="metric-value">{{ $card['value'] }}</p>
                <p class="metric-meta">{{ $card['meta'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[1.25fr_0.9fr]">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Team Activity</p>
                    <h3 class="panel-title mt-2">Recent Transactions</h3>
                    <p class="panel-subtitle">Latest stock changes recorded across the team.</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="btn btn-ghost">View all</a>
            </div>

            <div class="surface-list mt-5">
                @forelse ($recentTransactions as $transaction)
                    <div class="surface-item">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-950">{{ $transaction->product->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ ucfirst($transaction->type) }} by {{ $transaction->user->name }} · {{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="badge {{ $transaction->type === 'out' ? 'badge-rose' : ($transaction->type === 'adjustment' ? 'badge-amber' : 'badge-emerald') }}">
                                {{ $transaction->type === 'adjustment' ? 'Set to ' . $transaction->quantity_after : ucfirst($transaction->type) . ' ' . $transaction->quantity }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No transactions yet.</div>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Attention</p>
                    <h3 class="panel-title mt-2">Active Low-Stock Alerts</h3>
                    <p class="panel-subtitle">Products that crossed below their configured threshold.</p>
                </div>
                <a href="{{ route('alerts.index') }}" class="btn btn-ghost">Open alerts</a>
            </div>

            <div class="surface-list mt-5">
                @forelse ($activeAlerts as $alert)
                    <a href="{{ route('products.show', $alert->product) }}" class="surface-item-strong block">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-950">{{ $alert->product->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $alert->product->sku }} · {{ $alert->product->category?->name ?? 'No category' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-semibold text-amber-700">{{ $alert->product->quantity }}</p>
                                <p class="text-xs uppercase tracking-[0.18em] text-amber-700">Min {{ $alert->threshold }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state">No active low-stock alerts.</div>
                @endforelse
            </div>
        </article>
    </section>
@endsection
