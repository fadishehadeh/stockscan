<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ActivityLogService;
use App\Services\SkuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly SkuService $skuService,
    ) {
    }

    public function index(): View
    {
        return view('categories.index', [
            'categories' => Category::query()->withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = Category::query()->create($this->validatedData($request));

        $this->activityLogService->record('category.created', 'Created category ' . $category->name . '.', $request->user(), $category);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validatedData($request, $category->id));

        $this->activityLogService->record('category.updated', 'Updated category ' . $category->name . '.', $request->user(), $category);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'This category cannot be deleted while products still use it.']);
        }

        $name = $category->name;
        $category->delete();

        $this->activityLogService->record('category.deleted', 'Deleted category ' . $name . '.', $request->user());

        return back()->with('success', 'Category deleted successfully.');
    }

    private function validatedData(Request $request, ?int $categoryId = null): array
    {
        $uniqueName = 'unique:categories,name';
        $uniquePrefix = 'unique:categories,sku_prefix';
        if ($categoryId) {
            $uniqueName .= ',' . $categoryId;
            $uniquePrefix .= ',' . $categoryId;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', $uniqueName],
            'sku_prefix' => ['nullable', 'string', 'max:12', $uniquePrefix],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $data['sku_prefix'] = blank($data['sku_prefix'] ?? null)
            ? $this->skuService->generateCategoryPrefix($data['name'], $categoryId)
            : $this->skuService->ensureUniqueCategoryPrefix($data['sku_prefix'], $categoryId);

        return $data;
    }
}
