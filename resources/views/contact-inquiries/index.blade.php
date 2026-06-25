@extends('layouts.app', ['title' => 'Contact Inquiries · StockScan', 'heading' => 'Contact Inquiries'])

@section('content')
    <section class="grid gap-4 md:grid-cols-3">
        <div class="metric-card">
            <p class="metric-label">Total inquiries</p>
            <p class="metric-value">{{ $summary['total'] }}</p>
            <p class="metric-helper">All contact form submissions stored in the app.</p>
        </div>
        <div class="metric-card metric-card-soft-success">
            <p class="metric-label">Delivered by email</p>
            <p class="metric-value">{{ $summary['delivered'] }}</p>
            <p class="metric-helper">Successfully handed off through the current mail channel.</p>
        </div>
        <div class="metric-card metric-card-soft-danger">
            <p class="metric-label">Needs review</p>
            <p class="metric-value">{{ $summary['failed'] }}</p>
            <p class="metric-helper">Saved in the database even if delivery failed.</p>
        </div>
    </section>

    <section class="panel mt-6">
        <div class="panel-header">
            <div class="max-w-2xl">
                <p class="eyebrow">Lead Capture</p>
                <h3 class="panel-title mt-2">Landing page inquiries</h3>
                <p class="panel-subtitle">Review every submission, delivery status, and contact message from the public website.</p>
            </div>
        </div>

        <form method="GET" class="filter-bar mt-6 grid gap-4 lg:grid-cols-4">
            <input type="search" name="search" value="{{ request('search') }}" class="input lg:col-span-2" placeholder="Search by name, email, company, or message">
            <select name="status" class="input">
                <option value="">All statuses</option>
                <option value="delivered" @selected(request('status') === 'delivered')>Delivered</option>
                <option value="failed" @selected(request('status') === 'failed')>Failed</option>
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="input">
            <input type="date" name="to" value="{{ request('to') }}" class="input">
            <button class="btn btn-secondary lg:col-span-4">Filter</button>
        </form>

        <div class="table-shell mt-6">
            <table class="min-w-full divide-y divide-slate-200/80 bg-white text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-4 py-4">Submitted</th>
                        <th class="px-4 py-4">Contact</th>
                        <th class="px-4 py-4">Business</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-4 py-4">Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80">
                    @forelse ($inquiries as $inquiry)
                        <tr class="table-row align-top">
                            <td class="px-4 py-4 table-cell-muted whitespace-nowrap">
                                {{ $inquiry->submitted_at?->format('d M Y H:i') ?? $inquiry->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-900">{{ $inquiry->name }}</div>
                                <div class="mt-1 text-slate-600">{{ $inquiry->email }}</div>
                                @if ($inquiry->phone)
                                    <div class="mt-1 text-slate-500">{{ $inquiry->phone }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-800">{{ $inquiry->company ?: 'No company' }}</div>
                                <div class="mt-1 text-slate-500">{{ $inquiry->business_type ?: 'Not specified' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="status-pill {{ $inquiry->email_delivered ? 'status-pill-success' : 'status-pill-danger' }}">
                                    {{ $inquiry->email_delivered ? 'Delivered' : 'Failed' }}
                                </span>
                                <div class="mt-2 text-xs text-slate-500">{{ $inquiry->recipient_email ?: 'No recipient set' }}</div>
                                @if ($inquiry->delivery_error)
                                    <div class="mt-2 rounded-2xl bg-rose-50 px-3 py-2 text-xs leading-5 text-rose-700">
                                        {{ $inquiry->delivery_error }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="max-w-xl whitespace-pre-line text-slate-700">{{ $inquiry->message }}</div>
                                @if ($inquiry->ip_address || $inquiry->user_agent)
                                    <div class="mt-3 text-xs text-slate-400">
                                        {{ $inquiry->ip_address ?: 'Unknown IP' }}
                                        @if ($inquiry->user_agent)
                                            <span class="mx-1">·</span>{{ \Illuminate\Support\Str::limit($inquiry->user_agent, 90) }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10">
                                <div class="empty-state">No inquiries found for the selected filters.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $inquiries->links() }}</div>
    </section>
@endsection
