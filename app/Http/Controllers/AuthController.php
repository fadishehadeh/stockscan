<?php

namespace App\Http\Controllers;

use App\Mail\OtpCodeMail;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\OtpService;
use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly OtpService $otpService,
        private readonly SessionService $sessionService
    ) {
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

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
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

        // Generate OTP and send via email
        $code = $this->otpService->generateOtp($user);

        if ($user->email) {
            Mail::to($user->email)->send(new OtpCodeMail($code, $user->name));
        }

        // Store user ID, email, and OTP code in session for verification
        $request->session()->put([
            'otp_user_id' => $user->id,
            'otp_email' => $user->email,
            'otp_code' => config('app.debug') ? $code : null,
        ]);

        $request->session()->regenerate();

        $message = 'OTP code sent to your email.';
        if (config('app.debug')) {
            $message .= " (Test code: {$code})";
        }

        return redirect()->route('otp.verify.form')->with('message', $message);
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();

        $this->activityLogService->record('user.logout', 'User logged out.', $user);

        if ($user) {
            $this->sessionService->recordLogout($user);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
