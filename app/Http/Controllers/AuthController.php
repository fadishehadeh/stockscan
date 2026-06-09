<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly ActivityLogService $activityLogService)
    {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials['is_active'] = true;

        if (! Auth::attempt($credentials, false)) {
            $this->activityLogService->record(
                'user.login_failed',
                'Failed login attempt.',
                null,
                null,
                [
                    'username' => $credentials['username'],
                    'ip' => $request->ip(),
                ]
            );

            return back()->withErrors([
                'username' => 'The username or password is incorrect.',
            ])->onlyInput('username');
        }

        $request->session()->regenerate();
        $this->activityLogService->record('user.login', 'User logged in.', $request->user());

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->activityLogService->record('user.logout', 'User logged out.', $request->user());
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
