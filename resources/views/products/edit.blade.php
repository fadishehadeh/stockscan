@extends('layouts.app', ['title' => 'Edit Product · StockScan', 'heading' => 'Edit Product'])

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div class="max-w-2xl">
                <p class="eyebrow">Product Update</p>
                <h3 class="panel-title mt-2">Edit {{ $product->name }}</h3>
                <p class="panel-subtitle">Update pricing, stock levels, description, and image while keeping the system-managed barcode and SKU intact.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('products.update', $product) }}" class="mt-6" enctype="multipart/form-data">
            @include('products._form')
        </form>
    </section>
@endsection
