<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $categoryId = $request->integer('category');
        $type = $request->input('type');

        $inventoryQuery = Product::query()->with('category')
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId));

        $movementQuery = StockTransaction::query()->with(['product.category', 'user'])
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($categoryId, fn ($query) => $query->whereHas('product', fn ($product) => $product->where('category_id', $categoryId)));

        $inventoryItems = (clone $inventoryQuery)->orderBy('name')->get();
        $movements = (clone $movementQuery)->latest()->limit(20)->get();

        return view('reports.index', [
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => compact('from', 'to', 'categoryId', 'type'),
            'inventoryItems' => $inventoryItems,
            'lowStockItems' => $inventoryItems->filter(fn (Product $product) => $product->isLowStock()),
            'movementTotals' => [
                'in' => (clone $movementQuery)->where('type', 'in')->sum('quantity'),
                'out' => (clone $movementQuery)->where('type', 'out')->sum('quantity'),
                'adjustment' => (clone $movementQuery)->where('type', 'adjustment')->count(),
            ],
            'inventoryValue' => $inventoryItems->sum(fn (Product $product) => $product->quantity * (float) $product->cost),
            'movements' => $movements,
        ]);
    }
}
