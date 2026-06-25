@extends('layouts.app', ['title' => 'Users · StockScan', 'heading' => 'User Access'])

@section('content')
    <section class="grid gap-6 xl:grid-cols-[0.85fr_1.15fr]">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Access Control</p>
                    <h3 class="panel-title mt-2">Create user</h3>
                    <p class="panel-subtitle">Add a username/password account for owner, staff, or purchase manager access.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="mt-6 space-y-4" data-prevent-double-submit>
                @csrf
                <div>
                    <label class="label">Full Name</label>
                    <input name="name" class="input" required>
                </div>
                <div>
                    <label class="label">Username</label>
                    <input name="username" class="input" required>
                </div>
                <div>
                    <label class="label">Email for Alerts</label>
                    <input name="email" type="email" class="input" placeholder="Optional notification email">
                </div>
                <div>
                    <label class="label">Role</label>
                    <select name="role" class="input">
                        <option value="staff">Staff</option>
                        <option value="purchase_manager">Purchase Manager</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <div>
                    <label class="label">Password</label>
                    <input name="password" type="password" class="input" required>
                </div>
                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-sky-600">
                    Active account
                </label>
                <button class="btn btn-primary w-full" data-submit-label="Create User">Create User</button>
            </form>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Accounts</p>
                    <h3 class="panel-title mt-2">Current users</h3>
                </div>
            </div>
            <div class="surface-list mt-5">
                @foreach ($users as $user)
                    <form method="POST" action="{{ route('users.update', $user) }}" class="panel-muted" data-prevent-double-submit>
                        @csrf
                        @method('PUT')
                        <div class="product-form-grid">
                            <div>
                                <label class="label">Full Name</label>
                                <input name="name" value="{{ $user->name }}" class="input" required>
                            </div>
                            <div>
                                <label class="label">Username</label>
                                <input name="username" value="{{ $user->username }}" class="input" required>
                            </div>
                            <div>
                                <label class="label">Email for Alerts</label>
                                <input name="email" type="email" value="{{ $user->email }}" class="input" placeholder="Optional notification email">
                            </div>
                            <div>
                                <label class="label">Role</label>
                                <select name="role" class="input">
                                    <option value="staff" @selected(in_array($user->role, ['staff', 'user'], true))>Staff</option>
                                    <option value="purchase_manager" @selected($user->role === 'purchase_manager')>Purchase Manager</option>
                                    <option value="owner" @selected(in_array($user->role, ['owner', 'admin', 'super_admin'], true))>Owner</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="label">New Password</label>
                                <input name="password" type="password" class="input" placeholder="Leave blank to keep current password">
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <label class="flex items-center gap-3 text-sm text-slate-600">
                                <input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="h-4 w-4 rounded border-slate-300 text-sky-600">
                                Active account
                            </label>
                            <button class="btn btn-secondary" data-submit-label="Save User">Save User</button>
                        </div>
                    </form>
                @endforeach
            </div>
        </article>
    </section>
@endsection
