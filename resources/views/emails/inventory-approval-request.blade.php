<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Inventory approval needed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6;">
    <h1 style="font-size: 20px; margin-bottom: 16px;">Inventory approval needed</h1>

    <p><strong>Request type:</strong> {{ strtoupper(str_replace('_', ' ', $approvalRequest->type)) }}</p>
    <p><strong>Requested by:</strong> {{ $approvalRequest->requester?->name ?? 'Unknown user' }}</p>
    <p><strong>Submitted at:</strong> {{ $approvalRequest->created_at->format('Y-m-d H:i:s') }}</p>

    @if ($approvalRequest->type === \App\Models\InventoryApprovalRequest::TYPE_PRODUCT_CREATE)
        <p><strong>Product:</strong> {{ $approvalRequest->payload['name'] ?? 'New product' }}</p>
        <p><strong>Serial Number:</strong> {{ $approvalRequest->payload['serial_number'] ?? 'Not set' }}</p>
        <p><strong>Quantity:</strong> {{ $approvalRequest->payload['quantity'] ?? 0 }}</p>
        <p><strong>Unit Cost:</strong> {{ number_format((float) ($approvalRequest->payload['cost'] ?? 0), 2) }}</p>
    @else
        <p><strong>Product:</strong> {{ $approvalRequest->product?->name ?? ($approvalRequest->payload['product_name'] ?? 'Unknown product') }}</p>
        <p><strong>Quantity:</strong> {{ $approvalRequest->payload['quantity'] ?? 0 }}</p>
        <p><strong>Note:</strong> {{ $approvalRequest->payload['note'] ?? 'No note' }}</p>
    @endif

    <p style="margin-top: 24px;">
        Review the request in StockScan:
        <a href="{{ route('approvals.index') }}">{{ route('approvals.index') }}</a>
    </p>
</body>
</html>
