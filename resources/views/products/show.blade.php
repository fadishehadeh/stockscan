@extends('layouts.app', ['title' => $product->name . ' - StockScan', 'heading' => 'Product Detail'])

@section('content')
    <section class="space-y-6">
        <article class="panel">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-3">
                    <p class="eyebrow">{{ $product->sku }}</p>
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-3xl font-semibold tracking-tight text-slate-950">{{ $product->name }}</h3>
                            @if ($product->isArchived())
                                <span class="badge badge-slate">Archived</span>
                            @endif
                        </div>
                        <p class="max-w-2xl text-sm leading-6 text-slate-500">{{ $product->description ?: 'No description provided.' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('products.label', ['product' => $product, 'autoprint' => 1]) }}" target="_blank" class="btn btn-secondary">Print Sticker</a>
                    @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit Product</a>
                    @endif
                </div>
            </div>

            <div class="mt-6 product-summary-grid">
                <div class="space-y-5">
                    <div class="product-metric-strip">
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

                    <div class="product-detail-cards">
                        <div class="detail-card">
                            <span class="detail-label">Category</span>
                            <span class="detail-value">{{ $product->category?->name ?? 'No category' }}</span>
                        </div>
                        <div class="detail-card">
                            <span class="detail-label">Selling Price</span>
                            <span class="detail-value">{{ $product->selling_price !== null ? '$' . number_format($product->selling_price, 2) : 'Not set' }}</span>
                        </div>
                        <div class="detail-card">
                            <span class="detail-label">Barcode</span>
                            <span class="detail-value detail-value-mono">{{ $product->barcode }}</span>
                        </div>
                        <div class="detail-card">
                            <span class="detail-label">Serial Number</span>
                            <span class="detail-value detail-value-mono">{{ $product->serial_number ?: 'Not set' }}</span>
                        </div>
                    </div>

                    @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                        <div class="panel-muted">
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <p class="text-sm font-medium text-slate-500">Low Stock Threshold</p>
                                <span class="text-xs uppercase tracking-[0.18em] text-slate-400">Quick edit</span>
                            </div>
                            <form method="POST" action="{{ route('products.updateThreshold', $product) }}" class="flex gap-3" data-prevent-double-submit>
                                @csrf
                                <div class="flex-1">
                                    <input type="number" name="min_stock" value="{{ $product->min_stock }}" min="0" class="input" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                        </div>
                    @endif

                    <div class="panel-muted product-barcode-panel">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium text-slate-500">Barcode Label</p>
                            <span class="text-xs uppercase tracking-[0.18em] text-slate-400">Print ready</span>
                        </div>
                        <div class="mt-3 overflow-x-auto rounded-[1.4rem] bg-white p-4">
                            {!! $barcodeSvg !!}
                            <p class="mt-3 font-mono text-sm text-slate-600">{{ $product->barcode }}</p>
                        </div>
                    </div>
                </div>

                <div class="panel-muted product-image-panel">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-medium text-slate-500">Product Image</p>
                        <span class="text-xs uppercase tracking-[0.18em] text-slate-400">Preview</span>
                    </div>
                    @if ($product->image_path)
                        <div class="mt-3 -mx-4 -mb-4 px-4 pb-4">
                            <img id="product-image" src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full rounded-[0.25rem] object-cover cursor-pointer hover:opacity-90 transition-opacity h-64">
                        </div>
                    @else
                        <div class="empty-state mt-3 py-12">No product image uploaded.</div>
                    @endif
                </div>
            </div>

            @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                <div class="mt-6 flex flex-wrap gap-3">
                    @if (! $product->isArchived())
                        <form method="POST" action="{{ route('products.archive', $product) }}">
                            @csrf
                            <button class="btn btn-secondary" data-confirm="Archive this product and remove it from active stock workflows?">Archive Product</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('products.restore', $product) }}">
                            @csrf
                            <button class="btn btn-secondary">Restore Product</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" data-confirm="Delete this product and its stock history?">Delete Product</button>
                    </form>
                </div>
            @endif
        </article>

        <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Stock Action</p>
                        <h3 class="panel-title mt-2">Quick Stock Update</h3>
                        <p class="panel-subtitle">Record stock in, stock out, or a direct quantity adjustment from the product page.</p>
                    </div>
                </div>

                @if ($product->isArchived())
                    <div class="empty-state mt-6">This product is archived. Restore it first to resume stock updates and scanning.</div>
                @else
                    @if (! auth()->user()->isPurchaseManager())
                    <form method="POST" action="{{ route('transactions.store') }}" class="mt-6 space-y-4" data-prevent-double-submit>
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="grid gap-4 md:grid-cols-3">
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
                        </div>
                        <div>
                            <label class="label" for="note">Note</label>
                            <textarea id="note" name="note" rows="3" class="input" placeholder="Optional note for the movement"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button class="btn btn-primary min-w-48" data-submit-label="Save Movement">Save Movement</button>
                        </div>
                    </form>
                    @else
                        <div class="empty-state mt-6">Purchase managers review and approve stock requests but cannot submit direct stock movements.</div>
                    @endif
                @endif
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
                                    <p class="mt-1 text-sm text-slate-500">{{ $transaction->user->name }} - {{ $transaction->created_at->format('d M Y H:i') }}</p>
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
    </section>

    <!-- Image Lightbox Modal -->
    @if ($product->image_path)
        <div id="image-lightbox" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur p-4">
            <div class="relative max-w-4xl w-full">
                <img id="lightbox-image" src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full h-auto rounded-[0.3rem] max-h-[85vh] object-contain">
                <button type="button" id="lightbox-close" class="absolute top-4 right-4 bg-white hover:bg-gray-100 rounded-full p-2 transition">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <script>
            setTimeout(function() {
                const img = document.getElementById('product-image');
                const modal = document.getElementById('image-lightbox');
                const closeBtn = document.getElementById('lightbox-close');

                console.log('Product image:', img);
                console.log('Modal:', modal);
                console.log('Close button:', closeBtn);

                if (img && modal) {
                    img.onclick = function() {
                        console.log('Image clicked');
                        modal.style.display = 'flex';
                    };
                }

                if (closeBtn && modal) {
                    closeBtn.onclick = function(e) {
                        e.stopPropagation();
                        console.log('Close clicked');
                        modal.style.display = 'none';
                    };
                }

                if (modal) {
                    modal.onclick = function(e) {
                        if (e.target === modal) {
                            modal.style.display = 'none';
                        }
                    };
                }

                document.onkeydown = function(e) {
                    if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
                        modal.style.display = 'none';
                    }
                };
            }, 500);
        </script>
    @endif
@endsection
