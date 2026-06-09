@extends('layouts.app', ['title' => 'Alerts · StockScan', 'heading' => 'Low-Stock Alerts'])

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div class="max-w-2xl">
                <p class="eyebrow">Attention Queue</p>
                <h3 class="panel-title mt-2">Active alerts</h3>
                <p class="panel-subtitle">Products currently at or below their low-stock threshold.</p>
            </div>
        </div>

        <form method="GET" class="filter-bar mt-6 grid gap-4 lg:grid-cols-[1fr_auto]">
            <select name="category" class="input">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary">Filter</button>
        </form>

        <div class="surface-list mt-6">
            @forelse ($alerts as $alert)
                <a href="{{ route('products.show', $alert->product) }}" class="surface-item-strong block">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-950">{{ $alert->product->name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $alert->product->sku }} · {{ $alert->product->category?->name ?? 'No category' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-semibold text-amber-800">{{ $alert->product->quantity }} left</p>
                            <p class="text-sm text-amber-700">Threshold {{ $alert->threshold }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state">No active low-stock alerts.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $alerts->links() }}</div>
    </section>
@endsection
