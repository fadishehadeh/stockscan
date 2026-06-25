@extends('layouts.app', ['title' => 'Approvals · StockScan', 'heading' => 'Approval Queue'])

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div class="max-w-2xl">
                <p class="eyebrow">Approval Workflow</p>
                <h3 class="panel-title mt-2">Pending inventory requests</h3>
                <p class="panel-subtitle">Review new product requests and stock movements before they affect inventory.</p>
            </div>
        </div>

        <form method="GET" class="filter-bar mt-6 grid gap-4 lg:grid-cols-4">
            <select name="status" class="input">
                <option value="">All statuses</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
            <select name="type" class="input">
                <option value="">All request types</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                @endforeach
            </select>
            <select name="requester" class="input">
                <option value="">All requesters</option>
                @foreach ($requesters as $requester)
                    <option value="{{ $requester->id }}" @selected((string) request('requester') === (string) $requester->id)>{{ $requester->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary">Filter</button>
        </form>

        <div class="surface-list mt-6">
            @forelse ($approvals as $approval)
                @php
                    $requestCategory = $approval->product?->category?->name;

                    if (! $requestCategory && filled($approval->payload['category_id'] ?? null)) {
                        $requestCategory = $categories->get($approval->payload['category_id'])?->name;
                    }
                @endphp

                <article class="panel-muted">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge badge-slate">{{ strtoupper(str_replace('_', ' ', $approval->type)) }}</span>
                                <span class="badge {{ $approval->status === 'approved' ? 'badge-emerald' : ($approval->status === 'rejected' ? 'badge-rose' : 'badge-amber') }}">
                                    {{ ucfirst($approval->status) }}
                                </span>
                            </div>
                            <h4 class="text-lg font-semibold text-slate-950">
                                @if ($approval->type === \App\Models\InventoryApprovalRequest::TYPE_PRODUCT_CREATE)
                                    {{ $approval->payload['name'] ?? 'New product request' }}
                                @else
                                    {{ $approval->product?->name ?? ($approval->payload['product_name'] ?? 'Product request') }}
                                @endif
                            </h4>
                            <p class="text-sm text-slate-500">
                                Requested by {{ $approval->requester?->name ?? 'Unknown user' }}
                                · {{ $approval->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        @if ($approval->status === 'pending')
                            <div class="flex flex-wrap gap-3">
                                <form method="POST" action="{{ route('approvals.approve', $approval) }}">
                                    @csrf
                                    <button class="btn btn-success">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('approvals.reject', $approval) }}" class="flex flex-wrap gap-3">
                                    @csrf
                                    <input type="text" name="rejection_note" class="input min-w-56" placeholder="Optional rejection note">
                                    <button class="btn btn-danger">Reject</button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                        @if ($approval->type === \App\Models\InventoryApprovalRequest::TYPE_PRODUCT_CREATE)
                            <div class="detail-card">
                                <span class="detail-label">Category</span>
                                <span class="detail-value">{{ $requestCategory ?: 'No category' }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label">Serial Number</span>
                                <span class="detail-value">{{ $approval->payload['serial_number'] ?? 'Not set' }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label">Quantity</span>
                                <span class="detail-value">{{ $approval->payload['quantity'] ?? 0 }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label">Unit Cost</span>
                                <span class="detail-value">${{ number_format((float) ($approval->payload['cost'] ?? 0), 2) }}</span>
                            </div>
                        @else
                            <div class="detail-card">
                                <span class="detail-label">Quantity</span>
                                <span class="detail-value">{{ $approval->payload['quantity'] ?? 0 }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label">Unit Cost</span>
                                <span class="detail-value">
                                    {{ array_key_exists('unit_cost', $approval->payload ?? []) && $approval->payload['unit_cost'] !== null ? '$' . number_format((float) $approval->payload['unit_cost'], 2) : 'N/A' }}
                                </span>
                            </div>
                            <div class="detail-card md:col-span-2">
                                <span class="detail-label">Note</span>
                                <span class="detail-value">{{ $approval->payload['note'] ?? 'No note' }}</span>
                            </div>
                        @endif
                    </div>

                    @if ($approval->rejection_note)
                        <div class="mt-4 rounded-[0.3rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                            <strong>Rejection note:</strong> {{ $approval->rejection_note }}
                        </div>
                    @endif
                </article>
            @empty
                <div class="empty-state">No approval requests found.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $approvals->links() }}</div>
    </section>
@endsection
