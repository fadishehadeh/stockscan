<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Product;
use App\Services\ActivityLogService;
use App\Services\BarcodeService;
use App\Services\LowStockAlertService;
use App\Services\SkuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly BarcodeService $barcodeService,
        private readonly LowStockAlertService $lowStockAlertService,
        private readonly ActivityLogService $activityLogService,
        private readonly SkuService $skuService,
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $categoryId = $request->integer('category');
        $stock = $request->string('stock')->toString();

        $products = Product::query()
            ->with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($stock === 'low', fn ($query) => $query->whereColumn('quantity', '<=', 'min_stock'))
            ->when($stock === 'out', fn ($query) => $query->where('quantity', 0))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => compact('search', 'categoryId', 'stock'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('products.create', [
            'product' => new Product(),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $category = filled($data['category_id'] ?? null) ? Category::query()->find($data['category_id']) : null;
        $data['sku'] = $this->skuService->generateProductSku($category);
        $data['barcode'] = $this->barcodeService->generateUniqueCode(AppSetting::current());
        $data['image_path'] = $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : null;

        $product = Product::create($data);
        $this->lowStockAlertService->sync($product, false, $request->user());
        $this->activityLogService->record('product.created', 'Created product ' . $product->name . '.', $request->user(), $product);

        return redirect()->route('products.show', $product)->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'stockTransactions.user']);

        return view('products.show', [
            'product' => $product,
            'barcodeSvg' => $this->barcodeService->svg($product->barcode),
        ]);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $wasLowStock = $product->isLowStock();
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        $product->refresh();
        $this->lowStockAlertService->sync($product, $wasLowStock, $request->user());
        $this->activityLogService->record('product.updated', 'Updated product ' . $product->name . '.', $request->user(), $product);

        return redirect()->route('products.show', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $name = $product->name;
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        $this->activityLogService->record('product.deleted', 'Deleted product ' . $name . '.', $request->user());

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    public function label(Request $request, Product $product): View
    {
        $settings = AppSetting::current();
        $size = $request->string('size')->toString() ?: $settings->label_size_default;
        $labelConfigs = [
            'small' => ['width' => 280, 'padding' => 14, 'font' => 18, 'meta' => 12, 'price' => 0, 'scale' => 1, 'height' => 50],
            'medium' => ['width' => 360, 'padding' => 18, 'font' => 22, 'meta' => 14, 'price' => 28, 'scale' => 2, 'height' => 70],
            'large' => ['width' => 460, 'padding' => 24, 'font' => 28, 'meta' => 16, 'price' => 36, 'scale' => 2, 'height' => 90],
        ];
        $config = $labelConfigs[$size] ?? $labelConfigs['medium'];

        return view('products.label', [
            'product' => $product,
            'barcodeSvg' => $this->barcodeService->svg($product->barcode, $config['scale'], $config['height']),
            'labelConfig' => $config,
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'cost' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => [
                'nullable',
                File::image()
                    ->types(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                    ->max(4096),
            ],
        ]);
    }
}
