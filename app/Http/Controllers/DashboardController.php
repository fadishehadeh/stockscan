<?php

namespace App\Http\Controllers;

use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $products = Product::query();

        return view('dashboard.index', [
            'stats' => [
                'total_products' => (clone $products)->count(),
                'in_stock' => (clone $products)->where('quantity', '>', 0)->count(),
                'low_stock' => (clone $products)->whereColumn('quantity', '<=', 'min_stock')->count(),
                'out_of_stock' => (clone $products)->where('quantity', 0)->count(),
                'inventory_value' => (float) Product::query()->selectRaw('COALESCE(SUM(quantity * cost), 0) as total')->value('total'),
            ],
            'recentTransactions' => StockTransaction::query()->with(['product', 'user'])->latest()->limit(8)->get(),
            'activeAlerts' => LowStockAlert::query()->with('product.category')->where('status', 'active')->latest()->limit(8)->get(),
        ]);
    }
}
