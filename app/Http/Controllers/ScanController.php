<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Product;
use App\Services\BarcodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function __construct(private readonly BarcodeService $barcodeService)
    {
    }

    public function index(Request $request): View
    {
        $settings = AppSetting::current();

        return view('scan.index', [
            'product' => null,
            'barcodeSvg' => null,
            'notFoundBarcode' => $request->session()->get('not_found_barcode'),
            'settings' => $settings,
            'networkWarning' => $request->session()->get('network_warning'),
            'scanSimulatorSamples' => $this->scanSimulatorSamples(),
        ]);
    }

    public function lookup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'barcode' => ['required', 'string'],
        ]);
        $settings = AppSetting::current();

        $product = Product::query()->active()->where('barcode', $data['barcode'])->first();

        if (! $product) {
            return redirect()->route('scan.index')->with('not_found_barcode', $data['barcode']);
        }

        if ($settings->default_post_scan_behavior === 'open_product_actions') {
            return redirect()->route('scan.show', $product);
        }

        return redirect()->route('products.show', $product);
    }

    public function show(Product $product): View
    {
        $settings = AppSetting::current();

        return view('scan.index', [
            'product' => $product->load('category'),
            'barcodeSvg' => $this->barcodeService->svg($product->barcode),
            'notFoundBarcode' => null,
            'settings' => $settings,
            'networkWarning' => null,
            'scanSimulatorSamples' => $this->scanSimulatorSamples(),
        ]);
    }

    private function scanSimulatorSamples(): array
    {
        $samples = Product::query()
            ->active()
            ->select(['name', 'barcode', 'sku'])
            ->orderBy('name')
            ->limit(3)
            ->get()
            ->map(fn (Product $product) => [
                'label' => $product->name,
                'barcode' => $product->barcode,
                'sku' => $product->sku,
            ])
            ->all();

        $samples[] = [
            'label' => 'Not Found Demo',
            'barcode' => '999999999999',
            'sku' => 'UNKNOWN',
        ];

        return $samples;
    }
}
