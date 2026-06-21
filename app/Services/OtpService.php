<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;

class OtpService
{
    public function generateOtp(User $user, int $expiresInMinutes = 5): string
    {
        // Delete any existing unexpired OTPs for this user
        OtpCode::where('user_id', $user->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->delete();

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);

        return $code;
    }

    public function validateOtp(User $user, string $code): bool
    {
        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $code)
            ->where('is_used', false)
            ->first();

        if (!$otp) {
            return false;
        }

        if ($otp->isExpired()) {
            return false;
        }

        $otp->markAsUsed();
        return true;
    }

    public function cleanupExpiredOtps(): void
    {
        OtpCode::where('is_used', false)
            ->where('expires_at', '<', now())
            ->delete();
    }
}
