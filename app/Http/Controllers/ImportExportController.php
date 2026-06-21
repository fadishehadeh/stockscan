<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Services\ActivityLogService;
use App\Services\BarcodeService;
use App\Services\LowStockAlertService;
use App\Services\SkuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportController extends Controller
{
    public function __construct(
        private readonly BarcodeService $barcodeService,
        private readonly LowStockAlertService $lowStockAlertService,
        private readonly ActivityLogService $activityLogService,
        private readonly SkuService $skuService,
    ) {
    }

    public function showImportForm(): View
    {
        return view('imports.products');
    }

    public function importProducts(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($data['csv_file']->getRealPath(), 'r');
        $header = $handle ? fgetcsv($handle) : false;

        if (! $header) {
            return back()->withErrors(['csv_file' => 'The CSV file is empty or invalid.']);
        }

        $normalizedHeader = array_map(fn ($value) => strtolower(trim((string) $value)), $header);
        $requiredColumns = ['name', 'category', 'cost', 'selling_price', 'quantity', 'min_stock', 'description'];

        foreach ($requiredColumns as $column) {
            if (! in_array($column, $normalizedHeader, true)) {
                return back()->withErrors(['csv_file' => 'Missing required column: ' . $column]);
            }
        }

        $created = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $rowData = array_combine($normalizedHeader, $row);

            if (! $rowData || blank($rowData['name'])) {
                $errors[] = 'Row ' . $rowNumber . ': name is required.';
                continue;
            }

            $category = null;
            if (! blank($rowData['category'])) {
                $category = Category::query()->firstOrCreate(
                    ['name' => trim($rowData['category'])],
                    [
                        'description' => null,
                        'sku_prefix' => $this->skuService->generateCategoryPrefix(trim($rowData['category'])),
                    ]
                );
            }

            $sku = blank($rowData['sku'] ?? null)
                ? $this->skuService->generateProductSku($category)
                : trim($rowData['sku']);

            if (Product::query()->where('sku', $sku)->exists()) {
                $errors[] = 'Row ' . $rowNumber . ': sku already exists.';
                continue;
            }

            $product = Product::query()->create([
                'name' => trim($rowData['name']),
                'sku' => $sku,
                'barcode' => $this->barcodeService->generateUniqueCode(),
                'category_id' => $category?->id,
                'cost' => (float) ($rowData['cost'] ?: 0),
                'selling_price' => blank($rowData['selling_price']) ? null : (float) $rowData['selling_price'],
                'quantity' => max(0, (int) ($rowData['quantity'] ?: 0)),
                'min_stock' => max(0, (int) ($rowData['min_stock'] ?: 0)),
                'description' => blank($rowData['description']) ? null : trim($rowData['description']),
            ]);

            $this->activityLogService->record('product.imported', 'Imported product ' . $product->name . '.', $request->user(), $product);
            $this->lowStockAlertService->sync($product, false, $request->user());
            $created++;
        }

        fclose($handle);

        $this->activityLogService->record(
            'import.products',
            'Imported products CSV.',
            $request->user(),
            null,
            ['created' => $created, 'errors' => count($errors)]
        );

        return back()
            ->with('success', 'Import complete. Created: ' . $created . ($errors ? '. Some rows failed.' : ''))
            ->with('import_errors', $errors);
    }

    public function exportProducts(Request $request): StreamedResponse
    {
        $this->activityLogService->record('export.products', 'Exported products CSV.', $request->user());

        return response()->streamDownload(function () {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['name', 'sku', 'barcode', 'category', 'cost', 'selling_price', 'quantity', 'min_stock', 'description']);

            Product::query()->active()->with('category')->orderBy('name')->chunk(200, function ($products) use ($output) {
                foreach ($products as $product) {
                    fputcsv($output, [
                        $product->name,
                        $product->sku,
                        $product->barcode,
                        $product->category?->name,
                        $product->cost,
                        $product->selling_price,
                        $product->quantity,
                        $product->min_stock,
                        $product->description,
                    ]);
                }
            });

            fclose($output);
        }, 'products-export.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportTransactions(Request $request): StreamedResponse
    {
        $query = StockTransaction::query()
            ->with(['product', 'user'])
            ->when($request->filled('type'), fn ($builder) => $builder->where('type', $request->string('type')->toString()))
            ->when($request->filled('product'), fn ($builder) => $builder->where('product_id', $request->integer('product')))
            ->when($request->filled('from'), fn ($builder) => $builder->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($builder) => $builder->whereDate('created_at', '<=', $request->date('to')))
            ->latest();

        $this->activityLogService->record('export.transactions', 'Exported transactions CSV.', $request->user());

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['time', 'product', 'sku', 'type', 'quantity', 'before', 'after', 'user', 'note']);

            $query->chunk(200, function ($transactions) use ($output) {
                foreach ($transactions as $transaction) {
                    fputcsv($output, [
                        $transaction->created_at->format('Y-m-d H:i:s'),
                        $transaction->product->name,
                        $transaction->product->sku,
                        $transaction->type,
                        $transaction->quantity,
                        $transaction->quantity_before,
                        $transaction->quantity_after,
                        $transaction->user->name,
                        $transaction->note,
                    ]);
                }
            });

            fclose($output);
        }, 'transactions-export.csv', ['Content-Type' => 'text/csv']);
    }
}
