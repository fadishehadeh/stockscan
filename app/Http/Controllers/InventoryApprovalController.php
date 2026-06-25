<?php

namespace App\Http\Controllers;

use App\Models\InventoryApprovalRequest;
use App\Models\Category;
use App\Models\User;
use App\Services\InventoryApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryApprovalController extends Controller
{
    public function __construct(private readonly InventoryApprovalService $inventoryApprovalService)
    {
    }

    public function index(Request $request): View
    {
        $approvals = InventoryApprovalRequest::query()
            ->with(['requester', 'approver', 'product.category'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->toString()))
            ->when($request->filled('requester'), fn ($query) => $query->where('requester_user_id', $request->integer('requester')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('approvals.index', [
            'approvals' => $approvals,
            'categories' => Category::query()->orderBy('name')->get()->keyBy('id'),
            'requesters' => User::query()->orderBy('name')->get(),
            'types' => [
                InventoryApprovalRequest::TYPE_PRODUCT_CREATE,
                InventoryApprovalRequest::TYPE_STOCK_IN,
                InventoryApprovalRequest::TYPE_STOCK_OUT,
            ],
        ]);
    }

    public function approve(Request $request, InventoryApprovalRequest $approval): RedirectResponse
    {
        $this->inventoryApprovalService->approve($approval, $request->user());

        return back()->with('success', 'Approval request processed successfully.');
    }

    public function reject(Request $request, InventoryApprovalRequest $approval): RedirectResponse
    {
        $data = $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->inventoryApprovalService->reject($approval, $request->user(), $data['rejection_note'] ?? null);

        return back()->with('success', 'Approval request rejected.');
    }
}
