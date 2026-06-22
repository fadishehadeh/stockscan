@extends('layouts.app', ['heading' => 'Account Settings'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">Account Settings</h2>
            <p class="mt-1 text-sm text-gray-600">Manage your account information and security.</p>
        </div>

        @if (session('success'))
            <div class="p-6 bg-green-50 border-l-4 border-green-400">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <div class="p-6 space-y-6">
            <!-- User Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900">User Information</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" value="{{ auth()->user()->name }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" value="{{ auth()->user()->username }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <input type="text" value="{{ ucfirst(auth()->user()->role) }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1 flex items-center justify-between">
                            <input type="email" value="{{ auth()->user()->email }}" disabled class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                            <a href="{{ route('account.change-email') }}" class="ml-3 text-sm text-sky-600 hover:text-sky-700 font-medium">Change</a>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Member Since</label>
                        <input type="text" value="{{ auth()->user()->created_at->format('M d, Y') }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900">Security</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Password</p>
                            <p class="text-sm text-gray-600">Change your password regularly for better security</p>
                        </div>
                        <a href="{{ route('account.change-password') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">Change</a>
                    </div>
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Active Sessions</p>
                            <p class="text-sm text-gray-600">View and manage your login sessions</p>
                        </div>
                        <a href="{{ route('sessions.active') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">Manage</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
