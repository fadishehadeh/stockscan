@extends('layouts.app', ['title' => 'Products · StockScan', 'heading' => 'Products'])

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div class="max-w-2xl">
                <p class="eyebrow">Catalog</p>
                <h3 class="panel-title mt-2">Product inventory</h3>
                <p class="panel-subtitle">Search by name, SKU, or barcode, review stock health quickly, and move straight into product details or stock operations.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if (auth()->user()->isOwner())
                    <a href="{{ route('imports.products.show') }}" class="btn btn-secondary">Import CSV</a>
                    <a href="{{ route('exports.products') }}" class="btn btn-secondary">Export CSV</a>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
                @endif
            </div>
        </div>

        <form method="GET" class="filter-bar mt-6 grid gap-4 lg:grid-cols-[1.5fr_0.8fr_0.8fr_auto]">
            <input name="search" value="{{ $filters['search'] }}" class="input" placeholder="Search by name, SKU, or barcode">
            <select name="category" class="input">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($filters['categoryId'] === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="stock" class="input">
                <option value="">All stock states</option>
                <option value="low" @selected($filters['stock'] === 'low')>Low stock</option>
                <option value="out" @selected($filters['stock'] === 'out')>Out of stock</option>
            </select>
            <button class="btn btn-secondary">Filter</button>
        </form>

        <div class="table-shell mt-6">
            <table class="min-w-full divide-y divide-slate-200/80 bg-white text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-4 py-4">Product</th>
                        <th class="px-4 py-4">Barcode</th>
                        <th class="px-4 py-4">Cost</th>
                        <th class="px-4 py-4">Quantity</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-4 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80">
                    @forelse ($products as $product)
                        <tr class="table-row">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-14 w-14 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                        @if ($product->image_path)
                                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">
                                                IMG
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-950">{{ $product->name }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $product->sku }} · {{ $product->category?->name ?? 'No category' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-mono text-slate-600">{{ $product->barcode }}</td>
                            <td class="px-4 py-4">${{ number_format($product->cost, 2) }}</td>
                            <td class="px-4 py-4 font-semibold text-slate-950">{{ $product->quantity }}</td>
                            <td class="px-4 py-4">
                                @if ($product->quantity === 0)
                                    <span class="badge badge-rose">Out of stock</span>
                                @elseif ($product->isLowStock())
                                    <span class="badge badge-amber">Low stock</span>
                                @else
                                    <span class="badge badge-emerald">Healthy</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-ghost">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10">
                                <div class="empty-state">No products match your filters.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </section>
@endsection
