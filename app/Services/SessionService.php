<?php

namespace App\Services;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;

class SessionService
{
    public function recordLogin(User $user, Request $request): void
    {
        LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_name' => $this->parseDeviceName($request->userAgent()),
            'logged_in_at' => now(),
            'is_active' => true,
        ]);
    }

    public function recordLogout(User $user, ?LoginHistory $loginHistory = null): void
    {
        if ($loginHistory) {
            $loginHistory->logout();
        } else {
            // Logout the most recent active session
            LoginHistory::where('user_id', $user->id)
                ->where('is_active', true)
                ->latest('logged_in_at')
                ->first()
                ?->logout();
        }
    }

    public function terminateSession(int $sessionId, User $user): bool
    {
        $session = LoginHistory::find($sessionId);

        if (!$session || $session->user_id !== $user->id) {
            return false;
        }

        $session->logout();
        return true;
    }

    public function getActiveSessions(User $user)
    {
        return LoginHistory::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest('logged_in_at')
            ->get();
    }

    private function parseDeviceName(string $userAgent): string
    {
        if (stripos($userAgent, 'windows') !== false) {
            return 'Windows';
        } elseif (stripos($userAgent, 'macintosh') !== false) {
            return 'MacOS';
        } elseif (stripos($userAgent, 'linux') !== false) {
            return 'Linux';
        } elseif (stripos($userAgent, 'iphone') !== false) {
            return 'iPhone';
        } elseif (stripos($userAgent, 'android') !== false) {
            return 'Android';
        }
        return 'Unknown Device';
    }
}
