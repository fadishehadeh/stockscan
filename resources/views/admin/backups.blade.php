@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- B2 Configuration -->
        <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Backblaze B2 Configuration</h3>
                <p class="mt-1 text-sm text-gray-600">Configure cloud backup destination.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">B2 Application Key ID</label>
                        <input type="text" placeholder="Your Key ID" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500" value="{{ env('B2_APPLICATION_KEY_ID', '') }}">
                        <p class="mt-1 text-xs text-gray-500">Found in B2 account settings</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">B2 Application Key</label>
                        <input type="password" placeholder="Your Application Key" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500" value="{{ env('B2_APPLICATION_KEY', '') }}">
                        <p class="mt-1 text-xs text-gray-500">Keep this secret</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">B2 Bucket Name</label>
                        <input type="text" placeholder="my-backups-bucket" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500" value="{{ env('B2_BUCKET_NAME', '') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">B2 Bucket ID</label>
                        <input type="text" placeholder="Your Bucket ID" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500" value="{{ env('B2_BUCKET_ID', '') }}">
                    </div>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <span class="ml-3 text-sm text-gray-700">Enable B2 backups</span>
                    </label>
                    <p class="mt-2 text-xs text-gray-500">Backups will be automatically uploaded to Backblaze B2 when enabled.</p>
                </div>
                <div class="flex gap-2">
                    <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700">
                        Save Configuration
                    </button>
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Test Connection
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Database Backups
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage and restore database backups.
                    </p>
                </div>
                <form method="POST" action="{{ route('backups.create') }}" style="display:inline;">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700"
                    >
                        Create Backup
                    </button>
                </form>
            </div>

            @if (session('message'))
                <div class="p-6 bg-green-50 border-l-4 border-green-400">
                    <p class="text-sm text-green-700">{{ session('message') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="p-6 bg-red-50 border-l-4 border-red-400">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @if ($backups->isEmpty())
                <div class="p-6">
                    <p class="text-gray-500">No backups found. Create one to get started.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($backups as $backup)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $backup->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($backup->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($backup->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                        @elseif ($backup->status === 'failed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                        @elseif ($backup->status === 'in_progress')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">In Progress</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($backup->size)
                                            {{ number_format($backup->size / 1024 / 1024, 2) }} MB
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $backup->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        @if ($backup->status === 'completed')
                                            <form method="POST" action="{{ route('backups.restore', $backup->id) }}" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="confirm" value="1">
                                                <button
                                                    type="submit"
                                                    onclick="return confirm('Are you sure? This will restore your database to the backup state.')"
                                                    class="text-orange-600 hover:text-orange-900"
                                                >
                                                    Restore
                                                </button>
                                            </form>
                                        @endif

                                        @if ($backup->status === 'completed' && $backup->backup_path)
                                            <a href="{{ route('backups.download', $backup->id) }}" class="text-blue-600 hover:text-blue-900">
                                                Download
                                            </a>
                                        @endif

                                        <form method="POST" action="{{ route('backups.delete', $backup->id) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                onclick="return confirm('Delete this backup?')"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($backups->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $backups->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
