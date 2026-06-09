@extends('layouts.app', ['title' => $product->name . ' · StockScan', 'heading' => 'Product Detail'])

@section('content')
    <section class="product-hero">
        <article class="panel">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="eyebrow">{{ $product->sku }}</p>
                    <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $product->name }}</h3>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">{{ $product->description ?: 'No description provided.' }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('products.label', $product) }}" target="_blank" class="btn btn-secondary">Print Label</a>
                    @if (auth()->user()->isOwner())
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit Product</a>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_19rem]">
                <div class="space-y-6">
                    <div class="info-grid">
                        <div class="metric-card metric-card-highlight">
                            <span class="metric-label">Quantity</span>
                            <span class="metric-value">{{ $product->quantity }}</span>
                            <span class="metric-meta">Current stock</span>
                        </div>
                        <div class="metric-card">
                            <span class="metric-label">Min Stock</span>
                            <span class="metric-value">{{ $product->min_stock }}</span>
                            <span class="metric-meta">Alert threshold</span>
                        </div>
                        <div class="metric-card">
                            <span class="metric-label">Unit Cost</span>
                            <span class="metric-value">${{ number_format($product->cost, 2) }}</span>
                            <span class="metric-meta">Current cost</span>
                        </div>
                        <div class="metric-card">
                            <span class="metric-label">Inventory Value</span>
                            <span class="metric-value">${{ number_format($product->quantity * $product->cost, 2) }}</span>
                            <span class="metric-meta">Based on quantity</span>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="panel-muted">
                            <p class="text-sm font-medium text-slate-500">Category</p>
                            <p class="mt-2 font-semibold text-slate-950">{{ $product->category?->name ?? 'No category' }}</p>
                        </div>
                        <div class="panel-muted">
                            <p class="text-sm font-medium text-slate-500">Selling Price</p>
                            <p class="mt-2 font-semibold text-slate-950">{{ $product->selling_price !== null ? '$' . number_format($product->selling_price, 2) : 'Not set' }}</p>
                        </div>
                        <div class="panel-muted">
                            <p class="text-sm font-medium text-slate-500">Barcode</p>
                            <p class="mt-2 font-mono text-sm font-semibold text-slate-950">{{ $product->barcode }}</p>
                        </div>
                    </div>

                    <div class="panel-muted">
                        <p class="text-sm font-medium text-slate-500">Barcode Label</p>
                        <div class="mt-3 overflow-x-auto rounded-[1.4rem] bg-white p-4">
                            {!! $barcodeSvg !!}
                            <p class="mt-3 font-mono text-sm text-slate-600">{{ $product->barcode }}</p>
                        </div>
                    </div>
                </div>

                <div class="panel-muted">
                    <p class="text-sm font-medium text-slate-500">Product Image</p>
                    @if ($product->image_path)
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="mt-3 h-80 w-full rounded-[1.5rem] object-cover">
                    @else
                        <div class="empty-state mt-3 py-16">No product image uploaded.</div>
                    @endif
                </div>
            </div>

            @if (auth()->user()->isOwner())
                <form method="POST" action="{{ route('products.destroy', $product) }}" class="mt-6">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" data-confirm="Delete this product and its stock history?">Delete Product</button>
                </form>
            @endif
        </article>

        <article class="space-y-6">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Stock Action</p>
                        <h3 class="panel-title mt-2">Quick Stock Update</h3>
                        <p class="panel-subtitle">Record stock in, stock out, or a direct quantity adjustment from the product page.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('transactions.store') }}" class="mt-6 space-y-4" data-prevent-double-submit>
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div>
                        <label class="label" for="type">Movement Type</label>
                        <select id="type" name="type" class="input">
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="quantity">Quantity</label>
                        <input id="quantity" name="quantity" type="number" min="0" value="1" class="input" required>
                    </div>
                    <div>
                        <label class="label" for="unit_cost">Unit Cost for Stock In</label>
                        <input id="unit_cost" name="unit_cost" type="number" min="0" step="0.01" value="{{ $product->cost }}" class="input">
                    </div>
                    <div>
                        <label class="label" for="note">Note</label>
                        <textarea id="note" name="note" rows="3" class="input" placeholder="Optional note for the movement"></textarea>
                    </div>
                    <button class="btn btn-primary w-full" data-submit-label="Save Movement">Save Movement</button>
                </form>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">History</p>
                        <h3 class="panel-title mt-2">Recent Product History</h3>
                        <p class="panel-subtitle">Latest stock movements recorded for this item.</p>
                    </div>
                </div>
                <div class="surface-list mt-5">
                    @forelse ($product->stockTransactions->take(8) as $transaction)
                        <div class="surface-item">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-950">{{ ucfirst($transaction->type) }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $transaction->user->name }} · {{ $transaction->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-950">{{ $transaction->type === 'adjustment' ? 'Now ' . $transaction->quantity_after : $transaction->quantity }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $transaction->quantity_before }} -> {{ $transaction->quantity_after }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No stock movements recorded yet.</div>
                    @endforelse
                </div>
            </section>
        </article>
    </section>
@endsection
