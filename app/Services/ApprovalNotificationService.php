<?php

namespace App\Services;

use App\Mail\InventoryApprovalRequestMail;
use App\Models\InventoryApprovalRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ApprovalNotificationService
{
    /**
     * @return int number of notified recipients
     */
    public function notifyPurchaseManagers(InventoryApprovalRequest $approvalRequest): int
    {
        $recipients = User::query()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get()
            ->filter(fn (User $user) => $user->isPurchaseManager());

        $sent = 0;

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new InventoryApprovalRequestMail($approvalRequest->loadMissing('requester', 'product')));
                $sent++;
            } catch (Throwable $exception) {
                Log::warning('approval_request.notification_failed', [
                    'approval_request_id' => $approvalRequest->id,
                    'recipient_user_id' => $recipient->id,
                    'recipient_email' => $recipient->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $sent;
    }
}
