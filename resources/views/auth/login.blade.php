@extends('layouts.app', ['title' => 'Login · StockScan'])

@section('content')
    <div class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white/60 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.12)] lg:grid-cols-[1.1fr_0.9fr]">
        <section class="hidden bg-[linear-gradient(160deg,_#0f172a,_#0f766e)] p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-200">StockScan</p>
                <h1 class="mt-6 max-w-md text-4xl font-semibold leading-tight">A simple inventory system for barcode-driven teams.</h1>
                <p class="mt-4 max-w-md text-base text-slate-200">Track stock, update quantities, print labels, and review reports from one clean PHP app.</p>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="rounded-3xl border border-white/15 bg-white/10 p-4">
                    <p class="text-cyan-100">Scanner ready</p>
                    <p class="mt-2 text-2xl font-semibold">Code 128</p>
                </div>
                <div class="rounded-3xl border border-white/15 bg-white/10 p-4">
                    <p class="text-cyan-100">Roles</p>
                    <p class="mt-2 text-2xl font-semibold">Owner + Staff</p>
                </div>
            </div>
        </section>

        <section class="p-8 sm:p-10">
            @if (session('otp_user_id'))
                <!-- OTP Verification Form -->
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Verify Your Identity</p>
                <h2 class="mt-3 text-3xl font-semibold text-slate-900">Enter your verification code</h2>
                <p class="mt-2 text-sm text-slate-500">A 6-digit code has been sent to {{ session('otp_email') }}</p>

                @if (session('message'))
                    <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                        {{ session('message') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('otp.verify') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label class="label" for="otp">6-Digit Code</label>
                        <input
                            id="otp"
                            name="otp"
                            type="text"
                            inputmode="numeric"
                            maxlength="6"
                            class="input text-center text-2xl tracking-widest"
                            placeholder="000000"
                            required
                            autofocus
                        >
                        @error('otp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button class="btn btn-primary w-full">Verify Code</button>
                </form>

                <div class="mt-6 text-center space-y-2">
                    <form method="POST" action="{{ route('otp.resend') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="text-sm text-sky-600 hover:text-sky-700 font-medium">
                            Didn't receive a code? Resend
                        </button>
                    </form>
                    <div>
                        <a href="{{ route('otp.cancel') }}" class="text-sm text-slate-600 hover:text-slate-700">
                            Back to login
                        </a>
                    </div>
                </div>
            @else
                <!-- Password Login Form -->
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Welcome Back</p>
                <h2 class="mt-3 text-3xl font-semibold text-slate-900">Sign in to continue</h2>
                <p class="mt-2 text-sm text-slate-500">Use your username and password to access the stock system.</p>

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label class="label" for="username">Username</label>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" class="input" required autofocus>
                        @error('username')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="label" for="password">Password</label>
                        <input id="password" name="password" type="password" class="input" required>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button class="btn btn-primary w-full">Login</button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('password.request.form') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">
                        Forgot your password?
                    </a>
                </div>

                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    <p class="font-semibold text-slate-900">Demo accounts</p>
                    <p class="mt-2">Owner: <span class="font-mono">owner</span> / <span class="font-mono">password</span></p>
                    <p>Staff: <span class="font-mono">staff</span> / <span class="font-mono">password</span></p>
                </div>
            @endif
        </section>
    </div>
@endsection
