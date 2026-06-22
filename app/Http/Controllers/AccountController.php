<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function showSettings()
    {
        return view('account.settings', [
            'user' => auth()->user(),
        ]);
    }

    public function changeEmailForm()
    {
        return view('account.change-email', [
            'user' => auth()->user(),
        ]);
    }

    public function changeEmail(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Current password is incorrect.']);
        }

        auth()->user()->update(['email' => $request->email]);

        return redirect()->route('account.settings')->with('success', 'Email updated successfully.');
    }

    public function changePasswordForm()
    {
        return view('account.change-password', [
            'user' => auth()->user(),
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return redirect()->route('account.settings')->with('success', 'Password changed successfully.');
    }
}
