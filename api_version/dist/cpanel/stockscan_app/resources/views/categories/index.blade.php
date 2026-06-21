@extends('layouts.app', ['title' => 'Categories · StockScan', 'heading' => 'Categories'])

@section('content')
    <section class="grid gap-6 xl:grid-cols-[0.8fr_1.2fr]">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Category Setup</p>
                    <h3 class="panel-title mt-2">Create category</h3>
                    <p class="panel-subtitle">Manage product grouping and the SKU prefix used for category-based item numbering.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('categories.store') }}" class="mt-6 space-y-4" data-prevent-double-submit>
                @csrf
                <div>
                    <label class="label">Name</label>
                    <input name="name" class="input" required>
                </div>
                <div>
                    <label class="label">SKU Prefix</label>
                    <input name="sku_prefix" class="input" maxlength="12" placeholder="Auto-generated if left blank">
                </div>
                <div>
                    <label class="label">Description</label>
                    <input name="description" class="input" placeholder="Optional short description">
                </div>
                <button class="btn btn-primary w-full" data-submit-label="Create Category">Create Category</button>
            </form>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Current List</p>
                    <h3 class="panel-title mt-2">Current categories</h3>
                </div>
            </div>
            <div class="surface-list mt-5">
                @foreach ($categories as $category)
                    <div class="panel-muted">
                        <form method="POST" action="{{ route('categories.update', $category) }}" class="grid gap-4 md:grid-cols-[1fr_0.55fr_1fr_auto]" data-prevent-double-submit>
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="label">Name</label>
                                <input name="name" value="{{ $category->name }}" class="input" required>
                            </div>
                            <div>
                                <label class="label">SKU Prefix</label>
                                <input name="sku_prefix" value="{{ $category->sku_prefix }}" class="input" maxlength="12" required>
                            </div>
                            <div>
                                <label class="label">Description</label>
                                <input name="description" value="{{ $category->description }}" class="input">
                            </div>
                            <div class="self-end">
                                <button class="btn btn-secondary w-full" data-submit-label="Save Category">Save</button>
                            </div>
                        </form>
                        <div class="mt-4 flex items-center justify-between gap-3">
                            <p class="text-sm text-slate-500">{{ $category->products_count }} products · Prefix {{ $category->sku_prefix }}</p>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" data-confirm="Delete this category?">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
@endsection
