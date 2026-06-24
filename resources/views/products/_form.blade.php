@csrf
@if ($product->exists)
    @method('PUT')
@endif

<div class="product-form-grid">
    <div class="md:col-span-2">
        <label class="label" for="name">Product Name</label>
        <input id="name" name="name" value="{{ old('name', $product->name) }}" class="input" required>
    </div>

    <div>
        <label class="label">SKU</label>
        @if ($product->exists)
            <div class="readonly-field">{{ $product->sku }}</div>
        @else
            <div class="placeholder-field">
                SKU will be generated automatically from the selected category sequence when you save this product.
            </div>
        @endif
    </div>

    <div>
        <label class="label">Barcode</label>
        @if ($product->exists)
            <div class="readonly-field">{{ $product->barcode }}</div>
        @else
            <div class="placeholder-field">
                Barcode will be generated automatically when you save this product.
            </div>
        @endif
    </div>

    <div>
        <label class="label" for="category_id">Category</label>
        <select id="category_id" name="category_id" class="input">
            <option value="">No category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>
                    {{ $category->name }} ({{ $category->sku_prefix }})
                </option>
            @endforeach
        </select>
        <p class="label-hint">The selected category defines the automatic SKU prefix and numbering sequence.</p>
    </div>

    <div>
        <label class="label" for="image">Product Image</label>
        <input id="image" name="image" type="file" accept="image/*" class="input">
        <p class="label-hint">Upload a product photo to improve recognition in listings, details, and scanning workflows.</p>
        <div id="upload-progress" class="mt-3 hidden">
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <div class="h-2 rounded-[0.3rem] bg-gray-200 overflow-hidden">
                        <div id="progress-bar" class="h-full bg-orange-600 transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                <span id="progress-text" class="text-xs font-medium text-gray-600 min-w-12 text-right">0%</span>
            </div>
        </div>
        <div id="upload-success" class="mt-3 hidden flex items-center gap-2 px-3 py-2 rounded-[0.3rem] bg-emerald-50 border border-emerald-200">
            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-medium text-emerald-800">Image uploaded successfully</span>
        </div>
    </div>

    @if ($product->image_path)
        <div class="md:col-span-2">
            <label class="label">Current Image</label>
            <div class="overflow-hidden rounded-[1.7rem] border border-slate-200 bg-slate-50 p-3">
                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-52 w-full rounded-[1.3rem] object-cover">
            </div>
        </div>
    @endif

    <div>
        <label class="label" for="cost">Unit Cost</label>
        <input id="cost" name="cost" type="number" min="0" step="0.01" value="{{ old('cost', $product->cost ?? 0) }}" class="input" required>
    </div>

    <div>
        <label class="label" for="selling_price">Selling Price</label>
        <input id="selling_price" name="selling_price" type="number" min="0" step="0.01" value="{{ old('selling_price', $product->selling_price) }}" class="input">
    </div>

    <div>
        <label class="label" for="quantity">Current Quantity</label>
        <input id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $product->quantity ?? 0) }}" class="input" required>
    </div>

    <div>
        <label class="label" for="min_stock">Low Stock Threshold</label>
        <input id="min_stock" name="min_stock" type="number" min="0" value="{{ old('min_stock', $product->min_stock ?? 0) }}" class="input" required>
    </div>

    <div class="md:col-span-2">
        <label class="label" for="description">Description</label>
        <textarea id="description" name="description" rows="4" class="input">{{ old('description', $product->description) }}</textarea>
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button class="btn btn-primary">{{ $product->exists ? 'Save Changes' : 'Create Product' }}</button>
    <a href="{{ $product->exists ? route('products.show', $product) : route('products.index') }}" class="btn btn-secondary">Cancel</a>
</div>
