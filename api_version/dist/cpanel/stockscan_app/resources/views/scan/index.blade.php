@extends('layouts.app', ['title' => 'Scan · StockScan', 'heading' => 'Scan Barcode'])

@section('content')
    <section class="scan-shell">
        <article class="scan-zone">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="max-w-lg">
                    <p class="eyebrow">Scan Station</p>
                    <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Ready for continuous barcode input.</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Keep the cursor in the scan field. Your USB barcode scanner types into this screen like a keyboard and can submit automatically on Enter.</p>
                </div>
                <div class="panel-muted min-w-[12rem]">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Current mode</p>
                    <p class="mt-2 text-sm font-semibold text-slate-950">Keyboard Wedge</p>
                    <p class="mt-1 text-sm text-slate-500">Auto-submit: {{ $settings->auto_submit_on_enter ? 'Enabled' : 'Disabled' }}</p>
                </div>
            </div>

            @if ($networkWarning)
                <div class="scan-status scan-status-danger mt-5">
                    {{ $networkWarning }}
                </div>
            @endif

            <div id="scan-network-status" class="scan-status scan-status-danger mt-5 hidden">
                Network connection appears unavailable. Scans cannot be sent until the browser reconnects.
            </div>

            <form method="POST" action="{{ route('scan.lookup') }}" class="mt-6 space-y-4" data-prevent-double-submit data-scan-form>
                @csrf
                <div>
                    <label class="label" for="barcode">Scan or Enter Barcode</label>
                    <input id="barcode" name="barcode" value="{{ old('barcode', $notFoundBarcode) }}" class="input input-scan" placeholder="Waiting for scanner input" required data-autofocus data-auto-submit="{{ $settings->auto_submit_on_enter ? '1' : '0' }}">
                    <p class="label-hint">The field stays focused for repeated scans and supports manual entry as a fallback.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                    <button class="btn btn-primary w-full" data-submit-label="Find Product">Find Product</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">View Movements</a>
                </div>
            </form>

            @if ($notFoundBarcode)
                <div class="scan-status scan-status-warning mt-5">
                    Barcode <span class="font-mono">{{ $notFoundBarcode }}</span> was not found.
                    @if (auth()->user()->isOwner())
                        <a href="{{ route('products.create') }}" class="mt-2 block font-semibold text-amber-900">Create a new product and let the system generate a barcode</a>
                    @endif
                </div>
            @endif
        </article>

        <article class="panel">
            @if ($product)
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="eyebrow">{{ $product->sku }}</p>
                        <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $product->name }}</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ $product->category?->name ?? 'No category' }}</p>
                    </div>
                    <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">Open Detail</a>
                </div>

                <div class="info-grid mt-6">
                    <div class="metric-card metric-card-highlight">
                        <span class="metric-label">Current Stock</span>
                        <span class="metric-value">{{ $product->quantity }}</span>
                        <span class="metric-meta">Live quantity</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-label">Min Stock</span>
                        <span class="metric-value">{{ $product->min_stock }}</span>
                        <span class="metric-meta">Alert threshold</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-label">Cost</span>
                        <span class="metric-value">${{ number_format($product->cost, 2) }}</span>
                        <span class="metric-meta">Current unit cost</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-label">Status</span>
                        <span class="metric-value text-xl">
                            @if ($product->quantity === 0)
                                Out
                            @elseif ($product->isLowStock())
                                Low
                            @else
                                Healthy
                            @endif
                        </span>
                        <span class="metric-meta">Stock state</span>
                    </div>
                </div>

                <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
                    <div class="panel-muted">
                        <p class="text-sm font-medium text-slate-500">Barcode</p>
                        <div class="mt-3 overflow-x-auto rounded-[1.4rem] bg-white p-4">
                            {!! $barcodeSvg !!}
                            <p class="mt-3 font-mono text-sm text-slate-600">{{ $product->barcode }}</p>
                        </div>
                    </div>
                    <div class="panel-muted">
                        <p class="text-sm font-medium text-slate-500">Product image</p>
                        @if ($product->image_path)
                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="mt-3 h-48 w-full rounded-[1.4rem] object-cover">
                        @else
                            <div class="empty-state mt-3 py-12">No product image uploaded.</div>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('transactions.store') }}" class="mt-6 grid gap-4 lg:grid-cols-2" data-prevent-double-submit>
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="return_to_scan" value="1">
                    <div>
                        <label class="label">Action</label>
                        <select name="type" class="input">
                            <option value="out" @selected($settings->default_stock_action === 'out')>Stock Out</option>
                            <option value="in" @selected($settings->default_stock_action === 'in')>Stock In</option>
                            <option value="adjustment" @selected($settings->default_stock_action === 'adjustment')>Adjustment</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Quantity</label>
                        <input name="quantity" type="number" min="0" value="1" class="input" required>
                    </div>
                    <div>
                        <label class="label">Unit Cost for Stock In</label>
                        <input name="unit_cost" type="number" min="0" step="0.01" value="{{ $product->cost }}" class="input">
                    </div>
                    <div>
                        <label class="label">Note</label>
                        <input name="note" class="input" placeholder="Optional note">
                    </div>
                    <div class="lg:col-span-2 flex flex-wrap gap-3">
                        <button class="btn btn-primary" data-submit-label="Save Movement">Save Movement</button>
                        <a href="{{ route('scan.index') }}" class="btn btn-secondary">Scan Another</a>
                    </div>
                </form>
            @else
                <div class="empty-state flex min-h-[28rem] items-center justify-center">
                    <div class="max-w-md">
                        <p class="eyebrow">Scan Ready</p>
                        <h3 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Waiting for the next barcode.</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-500">Once a product is found, this area becomes the action workspace for stock in, stock out, and quantity adjustment.</p>
                    </div>
                </div>
            @endif
        </article>
    </section>
@endsection
