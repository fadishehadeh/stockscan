<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\LowStockAlert;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(Request $request): View
    {
        $alerts = LowStockAlert::query()
            ->with('product.category')
            ->where('status', 'active')
            ->when($request->filled('category'), fn ($query) => $query->whereHas('product', fn ($product) => $product->where('category_id', $request->integer('category'))))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('alerts.index', [
            'alerts' => $alerts,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }
}
