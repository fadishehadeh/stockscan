<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $reportData = $this->buildReportData($request);

        return view('reports.index', $reportData);
    }

    public function exportPdf(Request $request): Response
    {
        $reportData = $this->buildReportData($request);

        $pdf = Pdf::loadView('reports.pdf', $reportData)
            ->setPaper('a4', 'portrait');

        return $pdf->download('inventory-report.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildReportData(Request $request): array
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $categoryId = $request->integer('category');
        $type = $request->input('type');

        $inventoryQuery = Product::query()->active()->with('category')
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId));

        $movementQuery = StockTransaction::query()->with(['product.category', 'user'])
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($categoryId, fn ($query) => $query->whereHas('product', fn ($product) => $product->where('category_id', $categoryId)));

        $inventoryItems = (clone $inventoryQuery)->orderBy('name')->get();
        $movements = (clone $movementQuery)->latest()->limit(20)->get();

        return [
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
            'generatedAt' => now(),
        ];
    }
}
