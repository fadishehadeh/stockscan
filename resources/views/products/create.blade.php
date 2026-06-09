@extends('layouts.app', ['title' => 'New Product · StockScan', 'heading' => 'Add Product'])

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div class="max-w-2xl">
                <p class="eyebrow">New Product</p>
                <h3 class="panel-title mt-2">Create product record</h3>
                <p class="panel-subtitle">Add a new item with automatic barcode, category-driven SKU, product image, pricing, quantity, and low-stock threshold details.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('products.store') }}" class="mt-6" enctype="multipart/form-data">
            @include('products._form')
        </form>
    </section>
@endsection
