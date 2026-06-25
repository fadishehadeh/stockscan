<?php

namespace App\Mail;

use App\Models\InventoryApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InventoryApprovalRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public InventoryApprovalRequest $approvalRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'StockScan approval needed: ' . str_replace('_', ' ', $this->approvalRequest->type),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inventory-approval-request',
        );
    }
}
