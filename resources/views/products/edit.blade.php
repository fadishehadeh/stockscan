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

        <form id="product-form" method="POST" action="{{ route('products.update', $product) }}" class="mt-6" enctype="multipart/form-data">
            @include('products._form')
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('product-form');
            const imageInput = document.getElementById('image');
            const uploadProgress = document.getElementById('upload-progress');
            const uploadSuccess = document.getElementById('upload-success');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            imageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    uploadProgress.classList.remove('hidden');
                    uploadSuccess.classList.add('hidden');
                    progressBar.style.width = '0%';
                    progressText.textContent = '0%';
                }
            });

            form.addEventListener('submit', function(e) {
                if (imageInput.files.length === 0) return;

                e.preventDefault();

                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();

                uploadProgress.classList.remove('hidden');
                uploadSuccess.classList.add('hidden');

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percentComplete + '%';
                        progressText.textContent = percentComplete + '%';
                    }
                });

                xhr.addEventListener('load', function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        progressBar.style.width = '100%';
                        progressText.textContent = '100%';
                        uploadProgress.classList.add('hidden');
                        uploadSuccess.classList.remove('hidden');
                        setTimeout(() => {
                            window.location.href = xhr.responseURL || '{{ route("products.show", $product) }}';
                        }, 1500);
                    } else {
                        alert('Upload failed. Please try again.');
                        uploadProgress.classList.add('hidden');
                    }
                });

                xhr.addEventListener('error', function() {
                    alert('Upload error. Please try again.');
                    uploadProgress.classList.add('hidden');
                });

                xhr.open('POST', form.action);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            });
        });
    </script>
@endsection
