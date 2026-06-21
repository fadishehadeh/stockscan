<?php

namespace App\Http\Controllers;

use App\Mail\OtpCodeMail;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    public function verifyForm()
    {
        $userId = session('otp_user_id');
        abort_unless($userId, 403);

        return view('auth.verify-otp', [
            'email' => session('otp_email'),
        ]);
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

        return back()->with('message', 'OTP code sent to your email.');
    }
}
