<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function requestReset(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate(['email' => 'required|email|exists:users,email']);

            $user = User::where('email', $request->email)->first();
            $token = Str::random(60);

            PasswordResetToken::updateOrCreate(
                ['email' => $request->email],
                ['token' => $token, 'created_at' => now()]
            );

            $url = route('password.reset', ['token' => $token, 'email' => $request->email]);

            Mail::to($request->email)->send(new ResetPasswordMail($url, $user->name));

            return back()->with('message', 'Password reset link sent to your email.');
        }

        return view('auth.forgot-password');
    }

    public function resetForm(Request $request, string $token, string $email)
    {
        $resetToken = PasswordResetToken::where('email', $email)
            ->where('token', $token)
            ->first();

        abort_unless($resetToken, 404);

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $resetToken = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        abort_unless($resetToken, 404);

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        $resetToken->delete();

        return redirect(route('login'))->with('message', 'Password reset successfully. Please login.');
    }
}
