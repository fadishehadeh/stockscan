@extends('layouts.app', ['title' => 'Dashboard - StockScan', 'heading' => 'Dashboard'])

@section('content')
    @php
        $latestMovement = $recentTransactions->first();
        $hour = (int) now()->format('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        $healthyCount = max(0, $stats['total_products'] - $stats['low_stock']);
        $warningCount = max(0, $stats['low_stock'] - $stats['out_of_stock']);
        $trackedTotal = max(1, $stats['total_products']);
        $healthyPercent = round(($healthyCount / $trackedTotal) * 100);
        $warningPercent = round(($warningCount / $trackedTotal) * 100);
        $outPercent = round(($stats['out_of_stock'] / $trackedTotal) * 100);
    @endphp

    <section class="dashboard-shell">
        <section class="dashboard-command">
            <article class="dashboard-summary-card">
                <div class="dashboard-summary-head">
                    <div>
                        <p class="eyebrow">Operational Summary</p>
                        <h3 class="dashboard-summary-title">{{ $greeting }}, {{ auth()->user()->name }}.</h3>
                        <p class="dashboard-summary-copy">Low-stock risks, scan activity, and inventory value are all surfaced here for fast daily action.</p>
                    </div>
                    <a href="{{ route('scan.index') }}" class="btn btn-primary">Quick Scan</a>
                </div>

                <div class="dashboard-summary-grid">
                    <div class="dashboard-inline-stat dashboard-inline-stat-amber">
                        <span class="dashboard-inline-label">Low stock</span>
                        <span class="dashboard-inline-value">{{ $stats['low_stock'] }}</span>
                    </div>
                    <div class="dashboard-inline-stat dashboard-inline-stat-rose">
                        <span class="dashboard-inline-label">Out of stock</span>
                        <span class="dashboard-inline-value">{{ $stats['out_of_stock'] }}</span>
                    </div>
                    <div class="dashboard-inline-stat dashboard-inline-stat-slate">
                        <span class="dashboard-inline-label">Inventory value</span>
                        <span class="dashboard-inline-value">${{ number_format($stats['inventory_value'], 2) }}</span>
                    </div>
                    <div class="dashboard-inline-stat dashboard-inline-stat-sky">
                        <span class="dashboard-inline-label">Latest movement</span>
                        <span class="dashboard-inline-value dashboard-inline-value-sm">
                            @if ($latestMovement)
                                {{ ucfirst($latestMovement->type) }} · {{ $latestMovement->product->name }}
                            @else
                                No activity yet
                            @endif
                        </span>
                    </div>
                </div>
            </article>

            <aside class="dashboard-ops-card">
                <div class="dashboard-ops-head">
                    <div>
                        <p class="eyebrow">Quick Actions</p>
                        <h3 class="dashboard-ops-title">Find or act fast</h3>
                    </div>
                </div>

                <form method="GET" action="{{ route('products.index') }}" class="dashboard-product-search">
                    <label class="sr-only" for="dashboard_search">Search products</label>
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                    </svg>
                    <input id="dashboard_search" name="search" class="dashboard-product-search-input" placeholder="Search by name, SKU, or barcode">
                </form>

                <div class="dashboard-action-grid">
                    <a href="{{ route('scan.index') }}" class="dashboard-action-tile dashboard-action-tile-primary">
                        <span class="dashboard-action-title">Quick Scan</span>
                        <span class="dashboard-action-meta">Open the scan station</span>
                    </a>

                    @if (auth()->user()->isOwner())
                        <a href="{{ route('products.create') }}" class="dashboard-action-tile">
                            <span class="dashboard-action-title">Add Product</span>
                            <span class="dashboard-action-meta">Create a new item</span>
                        </a>
                    @else
                        <a href="{{ route('products.index') }}" class="dashboard-action-tile">
                            <span class="dashboard-action-title">Browse Products</span>
                            <span class="dashboard-action-meta">Review active stock</span>
                        </a>
                    @endif

                    <a href="{{ route('alerts.index') }}" class="dashboard-action-tile dashboard-action-tile-warning">
                        <span class="dashboard-action-title">Alerts</span>
                        <span class="dashboard-action-meta">{{ $stats['low_stock'] }} items need review</span>
                    </a>
                </div>
            </aside>
        </section>

        <section class="dashboard-metric-strip">
            @foreach ([
                ['label' => 'Total Products', 'value' => $stats['total_products'], 'meta' => 'Tracked catalog items', 'tone' => 'sky', 'icon' => 'stack'],
                ['label' => 'In Stock', 'value' => $stats['in_stock'], 'meta' => $healthyCount . ' healthy products ready now', 'tone' => 'emerald', 'icon' => 'check'],
                ['label' => 'Low Stock', 'value' => $stats['low_stock'], 'meta' => max(0, $stats['low_stock'] - $stats['out_of_stock']) . ' warning items before stockout', 'tone' => 'amber', 'icon' => 'warning'],
                ['label' => 'Out of Stock', 'value' => $stats['out_of_stock'], 'meta' => 'Immediate replenishment needed', 'tone' => 'rose', 'icon' => 'x'],
                ['label' => 'Inventory Value', 'value' => '$' . number_format($stats['inventory_value'], 2), 'meta' => 'Current cost basis across stock', 'tone' => 'slate', 'icon' => 'value'],
            ] as $card)
                <article class="dashboard-metric dashboard-metric-{{ $card['tone'] }}">
                    <div class="dashboard-metric-top">
                        <span class="dashboard-metric-icon dashboard-metric-icon-{{ $card['tone'] }}">
                            @switch($card['icon'])
                                @case('check')
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-8 8a1 1 0 0 1-1.42-.007l-4-4a1 1 0 1 1 1.415-1.414l3.293 3.293 7.295-7.296a1 1 0 0 1 1.411.01Z" clip-rule="evenodd" /></svg>
                                    @break
                                @case('warning')
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l5.58 9.92c.75 1.334-.213 2.981-1.742 2.981H4.419c-1.53 0-2.492-1.647-1.742-2.98l5.58-9.92ZM11 7a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0V7Zm-1 7a1.25 1.25 0 1 0 0-2.5A1.25 1.25 0 0 0 10 14Z" clip-rule="evenodd" /></svg>
                                    @break
                                @case('x')
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.47 4.47a.75.75 0 0 1 1.06 0L10 8.94l4.47-4.47a.75.75 0 1 1 1.06 1.06L11.06 10l4.47 4.47a.75.75 0 0 1-1.06 1.06L10 11.06l-4.47 4.47a.75.75 0 0 1-1.06-1.06L8.94 10 4.47 5.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                                    @break
                                @case('value')
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 2.75a.75.75 0 0 0-1.5 0v.443a4.501 4.501 0 0 0-.713 8.79l2.74.685a3 3 0 0 1-.527 5.953H8.5a3 3 0 0 1-2.897-2.223.75.75 0 0 0-1.45.386A4.501 4.501 0 0 0 9.25 19.807v.443a.75.75 0 0 0 1.5 0v-.443a4.501 4.501 0 0 0 .713-8.79l-2.74-.685A3 3 0 0 1 9.25 4.38h2.25a3 3 0 0 1 2.897 2.223.75.75 0 1 0 1.45-.386A4.501 4.501 0 0 0 10.75 3.193V2.75Z" /></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M3.5 4A1.5 1.5 0 0 1 5 2.5h10A1.5 1.5 0 0 1 16.5 4v12A1.5 1.5 0 0 1 15 17.5H5A1.5 1.5 0 0 1 3.5 16V4Zm3 1.5a.75.75 0 0 0 0 1.5h7a.75.75 0 0 0 0-1.5h-7Zm0 4a.75.75 0 0 0 0 1.5h7a.75.75 0 0 0 0-1.5h-7Zm0 4a.75.75 0 0 0 0 1.5h4a.75.75 0 0 0 0-1.5h-4Z" /></svg>
                            @endswitch
                        </span>
                        <p class="metric-label">{{ $card['label'] }}</p>
                    </div>
                    <p class="metric-value">{{ $card['value'] }}</p>
                    <p class="dashboard-metric-helper">{{ $card['meta'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="dashboard-overview-grid">
            <article class="panel dashboard-panel dashboard-panel-slate">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Stock Status</p>
                        <h3 class="panel-title mt-2">Inventory health overview</h3>
                        <p class="panel-subtitle">A quick visual split of healthy, warning, and blocked stock positions.</p>
                    </div>
                </div>

                <div class="dashboard-status-chart mt-5">
                    <div class="dashboard-status-track" aria-hidden="true">
                        <span class="dashboard-status-fill dashboard-status-fill-emerald" style="width: {{ $healthyPercent }}%"></span>
                        <span class="dashboard-status-fill dashboard-status-fill-amber" style="width: {{ $warningPercent }}%"></span>
                        <span class="dashboard-status-fill dashboard-status-fill-rose" style="width: {{ $outPercent }}%"></span>
                    </div>

                    <div class="dashboard-status-legend">
                        <div class="dashboard-status-pill dashboard-status-pill-emerald">
                            <span class="dashboard-status-dot"></span>
                            <span>Healthy</span>
                            <strong>{{ $healthyCount }}</strong>
                        </div>
                        <div class="dashboard-status-pill dashboard-status-pill-amber">
                            <span class="dashboard-status-dot"></span>
                            <span>Low stock</span>
                            <strong>{{ $warningCount }}</strong>
                        </div>
                        <div class="dashboard-status-pill dashboard-status-pill-rose">
                            <span class="dashboard-status-dot"></span>
                            <span>Out</span>
                            <strong>{{ $stats['out_of_stock'] }}</strong>
                        </div>
                    </div>
                </div>
            </article>

            <article class="panel dashboard-panel dashboard-panel-soft">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Latest Movement</p>
                        <h3 class="panel-title mt-2">Most recent activity</h3>
                        <p class="panel-subtitle">Use this to confirm the last recorded stock action without opening the full history page.</p>
                    </div>
                </div>

                <div class="dashboard-latest-movement mt-5">
                    @if ($latestMovement)
                        <div class="dashboard-latest-badge dashboard-latest-badge-{{ $latestMovement->type === 'out' ? 'rose' : ($latestMovement->type === 'adjustment' ? 'amber' : 'emerald') }}">
                            {{ ucfirst($latestMovement->type) }}
                        </div>
                        <div>
                            <p class="dashboard-latest-title">{{ $latestMovement->product->name }}</p>
                            <p class="dashboard-latest-meta">{{ $latestMovement->user->name }} · {{ $latestMovement->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="dashboard-latest-qty">
                            @if ($latestMovement->type === 'adjustment')
                                {{ $latestMovement->quantity_before }} → {{ $latestMovement->quantity_after }}
                            @else
                                {{ $latestMovement->quantity }}
                            @endif
                        </div>
                    @else
                        <div class="empty-state w-full">No stock activity has been recorded yet.</div>
                    @endif
                </div>
            </article>
        </section>

        <section class="dashboard-content-grid">
            <article class="panel dashboard-panel dashboard-panel-slate">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Team Activity</p>
                        <h3 class="panel-title mt-2">Recent Transactions</h3>
                        <p class="panel-subtitle">Latest stock changes recorded across the team, prioritized for fast review.</p>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="btn btn-ghost">View all</a>
                </div>

                <div class="surface-list mt-4">
                    @forelse ($recentTransactions as $transaction)
                        <a href="{{ route('products.show', $transaction->product) }}" class="surface-item dashboard-activity-item block">
                            <div class="dashboard-activity-top">
                                <div class="dashboard-activity-copy">
                                    <p class="font-semibold text-slate-950">{{ $transaction->product->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ ucfirst($transaction->type) }} by {{ $transaction->user->name }} · {{ $transaction->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="badge {{ $transaction->type === 'out' ? 'badge-rose' : ($transaction->type === 'adjustment' ? 'badge-amber' : 'badge-emerald') }}">
                                    {{ $transaction->type === 'adjustment' ? 'Set to ' . $transaction->quantity_after : ucfirst($transaction->type) . ' ' . $transaction->quantity }}
                                </span>
                            </div>
                            <div class="dashboard-activity-foot">
                                <span>{{ $transaction->product->sku }}</span>
                                <span>{{ $transaction->quantity_before }} → {{ $transaction->quantity_after }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="empty-state">No transactions yet.</div>
                    @endforelse
                </div>
            </article>

            <article class="panel dashboard-panel dashboard-panel-amber">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Attention</p>
                        <h3 class="panel-title mt-2">Active Low-Stock Alerts</h3>
                        <p class="panel-subtitle">Critical items are emphasized first so restocking work is obvious at a glance.</p>
                    </div>
                    <a href="{{ route('alerts.index') }}" class="btn btn-ghost">Open alerts</a>
                </div>

                <div class="surface-list mt-4">
                    @forelse ($activeAlerts as $alert)
                        @php
                            $isCritical = $alert->product->quantity === 0 || $alert->product->quantity < max(1, ceil($alert->threshold / 2));
                            $alertTone = $isCritical ? 'critical' : 'warning';
                        @endphp
                        <a href="{{ route('products.show', $alert->product) }}" class="surface-item-strong dashboard-alert-item dashboard-alert-item-{{ $alertTone }} block">
                            <div class="dashboard-alert-top">
                                <div>
                                    <p class="font-semibold text-slate-950">{{ $alert->product->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $alert->product->sku }} · {{ $alert->product->category?->name ?? 'No category' }}</p>
                                </div>
                                <span class="dashboard-alert-severity dashboard-alert-severity-{{ $alertTone }}">
                                    {{ $isCritical ? 'Critical' : 'Warning' }}
                                </span>
                            </div>
                            <div class="dashboard-alert-foot">
                                <div class="dashboard-alert-reading">
                                    <span class="dashboard-alert-reading-label">Remaining</span>
                                    <strong>{{ $alert->product->quantity }}</strong>
                                </div>
                                <div class="dashboard-alert-reading">
                                    <span class="dashboard-alert-reading-label">Threshold</span>
                                    <strong>{{ $alert->threshold }}</strong>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="empty-state">No active low-stock alerts.</div>
                    @endforelse
                </div>
            </article>
        </section>
    </section>
@endsection
