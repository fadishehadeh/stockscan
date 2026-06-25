<?php

namespace App\Services;

use App\Models\Category;
use App\Models\InventoryApprovalRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InventoryApprovalService
{
    public function __construct(
        private readonly ApprovalNotificationService $approvalNotificationService,
        private readonly ActivityLogService $activityLogService,
        private readonly InventoryService $inventoryService,
        private readonly BarcodeService $barcodeService,
        private readonly LowStockAlertService $lowStockAlertService,
        private readonly SkuService $skuService,
    ) {
    }

    public function submitProductCreate(array $data, User $requester, ?UploadedFile $image = null): InventoryApprovalRequest
    {
        $payload = Arr::only($data, [
            'name',
            'serial_number',
            'category_id',
            'cost',
            'selling_price',
            'quantity',
            'min_stock',
            'description',
        ]);

        if ($image) {
            $payload['image_path'] = $image->store('products/pending', 'public');
            $payload['image_original_name'] = $image->getClientOriginalName();
        }

        $request = InventoryApprovalRequest::query()->create([
            'requester_user_id' => $requester->id,
            'type' => InventoryApprovalRequest::TYPE_PRODUCT_CREATE,
            'status' => InventoryApprovalRequest::STATUS_PENDING,
            'payload' => $payload,
        ]);

        $this->activityLogService->record(
            'approval_request.submitted',
            'Submitted product creation request for ' . $payload['name'] . '.',
            $requester,
            null,
            [
                'approval_request_id' => $request->id,
                'type' => $request->type,
                'requester_user_id' => $requester->id,
                'serial_number' => $payload['serial_number'] ?? null,
            ]
        );

        $this->approvalNotificationService->notifyPurchaseManagers($request);

        return $request;
    }

    public function submitStockMovement(
        Product $product,
        string $type,
        int $quantity,
        User $requester,
        ?float $unitCost = null,
        ?string $note = null,
    ): InventoryApprovalRequest {
        if (! in_array($type, [InventoryApprovalRequest::TYPE_STOCK_IN, InventoryApprovalRequest::TYPE_STOCK_OUT], true)) {
            throw ValidationException::withMessages([
                'type' => 'Only stock in and stock out require approval.',
            ]);
        }

        $request = InventoryApprovalRequest::query()->create([
            'requester_user_id' => $requester->id,
            'product_id' => $product->id,
            'type' => $type,
            'status' => InventoryApprovalRequest::STATUS_PENDING,
            'payload' => [
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'note' => $note,
                'product_name' => $product->name,
                'quantity_snapshot' => $product->quantity,
            ],
        ]);

        $this->activityLogService->record(
            'approval_request.submitted',
            'Submitted ' . str_replace('_', ' ', $type) . ' request for ' . $product->name . '.',
            $requester,
            $product,
            [
                'approval_request_id' => $request->id,
                'type' => $request->type,
                'quantity' => $quantity,
                'requester_user_id' => $requester->id,
                'quantity_before' => $product->quantity,
            ]
        );

        $this->approvalNotificationService->notifyPurchaseManagers($request);

        return $request;
    }

    public function approve(InventoryApprovalRequest $approvalRequest, User $approver): InventoryApprovalRequest
    {
        if (! $approvalRequest->isPending()) {
            throw ValidationException::withMessages([
                'approval' => 'This approval request has already been processed.',
            ]);
        }

        return DB::transaction(function () use ($approvalRequest, $approver) {
            $approvalRequest->refresh();

            if (! $approvalRequest->isPending()) {
                throw ValidationException::withMessages([
                    'approval' => 'This approval request has already been processed.',
                ]);
            }

            $entity = null;
            $payload = $approvalRequest->payload ?? [];

            if ($approvalRequest->type === InventoryApprovalRequest::TYPE_PRODUCT_CREATE) {
                $category = filled($payload['category_id'] ?? null)
                    ? Category::query()->find($payload['category_id'])
                    : null;

                $product = Product::query()->create([
                    'name' => $payload['name'],
                    'sku' => $this->skuService->generateProductSku($category),
                    'barcode' => $this->barcodeService->generateUniqueCode(),
                    'serial_number' => $payload['serial_number'] ?? null,
                    'category_id' => $payload['category_id'] ?? null,
                    'cost' => $payload['cost'],
                    'selling_price' => $payload['selling_price'] ?? null,
                    'quantity' => $payload['quantity'],
                    'min_stock' => $payload['min_stock'],
                    'description' => $payload['description'] ?? null,
                    'image_path' => $payload['image_path'] ?? null,
                ]);

                $this->lowStockAlertService->sync($product, false, $approver);

                $entity = $product;

                $this->activityLogService->record(
                    'product.created_after_approval',
                    'Created product ' . $product->name . ' after approval.',
                    $approver,
                    $product,
                    [
                        'approval_request_id' => $approvalRequest->id,
                        'requester_user_id' => $approvalRequest->requester_user_id,
                        'approver_user_id' => $approver->id,
                        'serial_number' => $product->serial_number,
                    ]
                );
            } else {
                $product = Product::query()->active()->findOrFail($approvalRequest->product_id);
                $movementType = $approvalRequest->type === InventoryApprovalRequest::TYPE_STOCK_IN ? 'in' : 'out';
                $quantityBefore = $product->quantity;

                $this->inventoryService->record(
                    product: $product,
                    type: $movementType,
                    quantity: (int) $payload['quantity'],
                    user: $approvalRequest->requester,
                    unitCost: isset($payload['unit_cost']) ? (float) $payload['unit_cost'] : null,
                    note: $payload['note'] ?? null,
                    metadata: [
                        'approval_request_id' => $approvalRequest->id,
                        'approved_by_user_id' => $approver->id,
                    ],
                );

                $entity = $product->fresh();

                $this->activityLogService->record(
                    'approval_request.executed',
                    'Executed approved ' . $movementType . ' request for ' . $product->name . '.',
                    $approver,
                    $product,
                    [
                        'approval_request_id' => $approvalRequest->id,
                        'requester_user_id' => $approvalRequest->requester_user_id,
                        'approver_user_id' => $approver->id,
                        'quantity' => (int) $payload['quantity'],
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $product->fresh()->quantity,
                    ]
                );

                $this->activityLogService->record(
                    'stock.' . $movementType . '_executed_after_approval',
                    'Executed approved stock ' . $movementType . ' for ' . $product->name . '.',
                    $approver,
                    $product,
                    [
                        'approval_request_id' => $approvalRequest->id,
                        'requester_user_id' => $approvalRequest->requester_user_id,
                        'approver_user_id' => $approver->id,
                        'quantity' => (int) $payload['quantity'],
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $product->fresh()->quantity,
                    ]
                );
            }

            $approvalRequest->update([
                'approver_user_id' => $approver->id,
                'product_id' => $entity?->id ?? $approvalRequest->product_id,
                'status' => InventoryApprovalRequest::STATUS_APPROVED,
                'processed_at' => now(),
            ]);

            $this->activityLogService->record(
                'approval_request.approved',
                'Approved ' . str_replace('_', ' ', $approvalRequest->type) . ' request.',
                $approver,
                $entity,
                [
                    'approval_request_id' => $approvalRequest->id,
                    'requester_user_id' => $approvalRequest->requester_user_id,
                    'approver_user_id' => $approver->id,
                ]
            );

            return $approvalRequest->fresh(['requester', 'approver', 'product']);
        });
    }

    public function reject(InventoryApprovalRequest $approvalRequest, User $approver, ?string $rejectionNote = null): InventoryApprovalRequest
    {
        if (! $approvalRequest->isPending()) {
            throw ValidationException::withMessages([
                'approval' => 'This approval request has already been processed.',
            ]);
        }

        return DB::transaction(function () use ($approvalRequest, $approver, $rejectionNote) {
            $approvalRequest->refresh();

            if (! $approvalRequest->isPending()) {
                throw ValidationException::withMessages([
                    'approval' => 'This approval request has already been processed.',
                ]);
            }

            if ($approvalRequest->type === InventoryApprovalRequest::TYPE_PRODUCT_CREATE) {
                $imagePath = $approvalRequest->payload['image_path'] ?? null;

                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $approvalRequest->update([
                'approver_user_id' => $approver->id,
                'status' => InventoryApprovalRequest::STATUS_REJECTED,
                'rejection_note' => $rejectionNote,
                'processed_at' => now(),
            ]);

            $this->activityLogService->record(
                'approval_request.rejected',
                'Rejected ' . str_replace('_', ' ', $approvalRequest->type) . ' request.',
                $approver,
                $approvalRequest->product,
                [
                    'approval_request_id' => $approvalRequest->id,
                    'requester_user_id' => $approvalRequest->requester_user_id,
                    'approver_user_id' => $approver->id,
                    'rejection_note' => $rejectionNote,
                ]
            );

            return $approvalRequest->fresh(['requester', 'approver', 'product']);
        });
    }
}
