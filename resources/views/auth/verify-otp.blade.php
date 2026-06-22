@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verify Your Identity
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter the code sent to {{ $email }}
            </p>
        </div>

        <form method="POST" action="{{ route('otp.verify') }}" class="mt-8 space-y-6">
            @csrf

            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700">
                    6-Digit Code
                </label>
                <input
                    id="otp"
                    name="otp"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                    placeholder="000000"
                    required
                    autofocus
                >
                @error('otp')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
            >
                Verify Code
            </button>
        </form>

        <div class="text-center">
            <form method="POST" action="{{ route('otp.resend') }}" class="inline">
                @csrf
                <button
                    type="submit"
                    class="text-sm text-orange-600 hover:text-orange-500"
                >
                    Didn't receive a code? Resend
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
