<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    public function sendVerification(Request $request)
    {
        $user = auth()->user();

        if ($user->email_verified_at) {
            return back()->with('message', 'Email already verified.');
        }

        $url = route('verification.verify', ['token' => Str::random(60), 'email' => $user->email]);

        Mail::to($user->email)->send(new VerifyEmailMail($url, $user->name));

        return back()->with('message', 'Verification link sent to your email.');
    }

    public function verify(Request $request, string $token, string $email)
    {
        $user = User::where('email', $email)->first();

        abort_unless($user, 404);

        // In a real app, you'd validate the token properly
        $user->update(['email_verified_at' => now()]);

        return redirect(route('dashboard'))->with('message', 'Email verified successfully!');
    }
}
