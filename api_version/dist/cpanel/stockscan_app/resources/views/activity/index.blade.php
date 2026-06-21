@extends('layouts.app', ['title' => 'Activity Log · StockScan', 'heading' => 'Activity Log'])

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div class="max-w-2xl">
                <p class="eyebrow">Audit Trail</p>
                <h3 class="panel-title mt-2">System activity</h3>
                <p class="panel-subtitle">Review key system actions across products, stock movements, settings, imports, and low-stock alerts.</p>
            </div>
        </div>

        <form method="GET" class="filter-bar mt-6 grid gap-4 lg:grid-cols-4">
            <select name="action" class="input">
                <option value="">All actions</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                @endforeach
            </select>
            <select name="user" class="input">
                <option value="">All users</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) request('user') === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="input">
            <input type="date" name="to" value="{{ request('to') }}" class="input">
            <button class="btn btn-secondary lg:col-span-4">Filter</button>
        </form>

        <div class="table-shell mt-6">
            <table class="min-w-full divide-y divide-slate-200/80 bg-white text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-4 py-4">Time</th>
                        <th class="px-4 py-4">Action</th>
                        <th class="px-4 py-4">User</th>
                        <th class="px-4 py-4">Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80">
                    @forelse ($logs as $log)
                        <tr class="table-row">
                            <td class="px-4 py-4 table-cell-muted">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-4 font-mono text-xs text-slate-700">{{ $log->action }}</td>
                            <td class="px-4 py-4">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-4 py-4">{{ $log->message }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10">
                                <div class="empty-state">No activity records found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $logs->links() }}</div>
    </section>
@endsection
