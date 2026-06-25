<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\InventoryApprovalRequest;
use App\Services\InventoryApprovalService;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockTransactionController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly InventoryApprovalService $inventoryApprovalService,
    ) {
    }

    public function index(Request $request): View
    {
        $transactions = StockTransaction::query()
            ->with(['product', 'user'])
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->toString()))
            ->when($request->filled('product'), fn ($query) => $query->where('product_id', $request->integer('product')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
            'products' => Product::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
            'return_to_scan' => ['nullable', 'boolean'],
        ]);

        try {
            $product = Product::query()->active()->findOrFail($data['product_id']);

            if ($request->user()->isPurchaseManager()) {
                abort(403);
            }

            if (in_array($data['type'], ['in', 'out'], true)) {
                $approvalType = $data['type'] === 'in'
                    ? InventoryApprovalRequest::TYPE_STOCK_IN
                    : InventoryApprovalRequest::TYPE_STOCK_OUT;

                $approvalRequest = $this->inventoryApprovalService->submitStockMovement(
                    product: $product,
                    type: $approvalType,
                    quantity: (int) $data['quantity'],
                    requester: $request->user(),
                    unitCost: isset($data['unit_cost']) ? (float) $data['unit_cost'] : null,
                    note: $data['note'] ?? null,
                );

                $message = 'Stock movement submitted for approval. Request #' . $approvalRequest->id . ' is pending.';

                if ($request->boolean('return_to_scan')) {
                    return redirect()->route('scan.index')->with('success', $message);
                }

                return back()->with('success', $message);
            }

            $this->inventoryService->record(
                product: $product,
                type: $data['type'],
                quantity: (int) $data['quantity'],
                user: $request->user(),
                unitCost: isset($data['unit_cost']) ? (float) $data['unit_cost'] : null,
                note: $data['note'] ?? null,
            );
        } catch (ValidationException $exception) {
            throw $exception;
        }

        if ($request->boolean('return_to_scan')) {
            return redirect()->route('scan.index')->with('success', 'Stock movement saved. Ready for the next scan.');
        }

        return back()->with('success', 'Stock movement saved.');
    }
}
