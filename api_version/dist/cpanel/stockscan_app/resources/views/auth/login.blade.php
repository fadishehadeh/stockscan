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
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Welcome Back</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">Sign in to continue</h2>
            <p class="mt-2 text-sm text-slate-500">Use your username and password to access the stock system.</p>

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label class="label" for="username">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" class="input" required autofocus>
                </div>
                <div>
                    <label class="label" for="password">Password</label>
                    <input id="password" name="password" type="password" class="input" required>
                </div>
                <button class="btn btn-primary w-full">Login</button>
            </form>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                <p class="font-semibold text-slate-900">Demo accounts</p>
                <p class="mt-2">Owner: <span class="font-mono">owner</span> / <span class="font-mono">password</span></p>
                <p>Staff: <span class="font-mono">staff</span> / <span class="font-mono">password</span></p>
            </div>
        </section>
    </div>
@endsection
