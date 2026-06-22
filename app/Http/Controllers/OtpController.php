<?php

namespace App\Http\Controllers;

use App\Mail\OtpCodeMail;
use App\Models\User;
use App\Services\OtpService;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService,
        protected SessionService $sessionService
    ) {}

    public function verifyForm()
    {
        $userId = session('otp_user_id');
        abort_unless($userId, 403);

        // OTP form is now shown inline on login page
        return redirect()->route('login');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $userId = session('otp_user_id');
        abort_unless($userId, 403);

        $user = User::find($userId);
        abort_unless($user, 403);

        if (!$this->otpService->validateOtp($user, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        session()->forget(['otp_user_id', 'otp_email']);
        auth()->login($user);

        // Record login in history
        $this->sessionService->recordLogin($user, $request);

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request)
    {
        $userId = session('otp_user_id');
        abort_unless($userId, 403);

        $user = User::find($userId);
        abort_unless($user, 403);

        $code = $this->otpService->generateOtp($user);

        if ($user->email) {
            Mail::to($user->email)->send(new OtpCodeMail($code, $user->name));
        }

        $message = 'OTP code sent to your email.';
        if (config('app.debug')) {
            $message .= " (Test code: {$code})";
        }

        return back()->with('message', $message);
    }

    public function cancel()
    {
        session()->forget(['otp_user_id', 'otp_email']);
        return redirect()->route('login');
    }
}
