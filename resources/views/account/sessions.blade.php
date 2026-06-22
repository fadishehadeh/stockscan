@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">
                    Active Sessions
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Manage your active login sessions across devices.
                </p>
            </div>

            @if ($sessions->isEmpty())
                <div class="p-6">
                    <p class="text-gray-500">No active sessions found.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach ($sessions as $session)
                        <div class="p-6 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @if (strpos($session->device_name, 'Windows') !== false || strpos($session->device_name, 'Mac') !== false || strpos($session->device_name, 'Linux') !== false)
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 14h8m-8-4h8M9 9h6"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">
                                            {{ $session->device_name }}
                                        </h3>
                                        <div class="mt-1 text-sm text-gray-500 space-y-1">
                                            <p>IP Address: {{ $session->ip_address }}</p>
                                            <p>Logged in: {{ $session->logged_in_at->diffForHumans() }}</p>
                                            @if ($session->location)
                                                <p>Location: {{ $session->location }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                @if ($session->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                            @if ($session->is_active)
                                <div class="ml-4">
                                    <form method="POST" action="{{ route('sessions.terminate', $session->id) }}" style="display:inline;">
                                        @csrf
                                        <button
                                            type="submit"
                                            onclick="return confirm('Are you sure you want to terminate this session?')"
                                            class="text-sm text-red-600 hover:text-red-900"
                                        >
                                            Terminate
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
