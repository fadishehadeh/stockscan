@extends('layouts.app', ['title' => 'Import Products · StockScan', 'heading' => 'Import Products'])

@section('content')
    <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Bulk Upload</p>
                    <h3 class="panel-title mt-2">Import products CSV</h3>
                    <p class="panel-subtitle">Upload a CSV file to create products with generated barcodes and category-based SKUs.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('imports.products.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4" data-prevent-double-submit>
                @csrf
                <div>
                    <label class="label">CSV File</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" class="input" required>
                </div>
                <button class="btn btn-primary w-full" data-submit-label="Import Products">Import Products</button>
            </form>

            @if (session('import_errors'))
                <div class="flash-error mt-6 mb-0">
                    <p class="font-semibold">Rows with errors</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Format Guide</p>
                    <h3 class="panel-title mt-2">Required CSV columns</h3>
                    <p class="panel-subtitle">Use this exact header row. Barcode is always generated. SKU can be omitted and will be generated from the category prefix sequence.</p>
                </div>
            </div>
            <div class="mt-5 rounded-[1.4rem] border border-slate-200 bg-slate-50 p-4 font-mono text-sm text-slate-700">
                name,category,cost,selling_price,quantity,min_stock,description
            </div>
            <div class="surface-list mt-5">
                <div class="surface-item">Optional extra column: <span class="font-mono">sku</span>. If present and unique, it will be used.</div>
                <div class="surface-item">If <span class="font-mono">sku</span> is empty, the system generates the next SKU automatically.</div>
                <div class="surface-item">Barcode values are always system-generated and should not be included in the file.</div>
            </div>
        </article>
    </section>
@endsection
