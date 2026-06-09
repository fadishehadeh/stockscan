@extends('layouts.app', ['title' => 'Movements · StockScan', 'heading' => 'Stock Movements'])

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div class="max-w-2xl">
                <p class="eyebrow">History</p>
                <h3 class="panel-title mt-2">Movement history</h3>
                <p class="panel-subtitle">Review every inventory change by product, date, and action type.</p>
            </div>
            @if (auth()->user()->isOwner())
                <a href="{{ route('exports.transactions', request()->query()) }}" class="btn btn-secondary">Export CSV</a>
            @endif
        </div>

        <form method="GET" class="filter-bar mt-6 grid gap-4 lg:grid-cols-5">
            <select name="product" class="input">
                <option value="">All products</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected((string) request('product') === (string) $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
            <select name="type" class="input">
                <option value="">All types</option>
                <option value="in" @selected(request('type') === 'in')>Stock In</option>
                <option value="out" @selected(request('type') === 'out')>Stock Out</option>
                <option value="adjustment" @selected(request('type') === 'adjustment')>Adjustment</option>
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="input">
            <input type="date" name="to" value="{{ request('to') }}" class="input">
            <button class="btn btn-secondary">Filter</button>
        </form>

        <div class="table-shell mt-6">
            <table class="min-w-full divide-y divide-slate-200/80 bg-white text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-4 py-4">Time</th>
                        <th class="px-4 py-4">Product</th>
                        <th class="px-4 py-4">Type</th>
                        <th class="px-4 py-4">Qty</th>
                        <th class="px-4 py-4">Before -> After</th>
                        <th class="px-4 py-4">User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80">
                    @forelse ($transactions as $transaction)
                        <tr class="table-row">
                            <td class="px-4 py-4 table-cell-muted">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-950">{{ $transaction->product->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $transaction->product->sku }}</p>
                            </td>
                            <td class="px-4 py-4"><span class="badge {{ $transaction->type === 'out' ? 'badge-rose' : ($transaction->type === 'adjustment' ? 'badge-amber' : 'badge-emerald') }}">{{ ucfirst($transaction->type) }}</span></td>
                            <td class="px-4 py-4">{{ $transaction->quantity }}</td>
                            <td class="px-4 py-4">{{ $transaction->quantity_before }} -> {{ $transaction->quantity_after }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $transaction->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10">
                                <div class="empty-state">No transactions found for this filter set.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    </section>
@endsection
