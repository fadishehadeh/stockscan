@extends('layouts.app', ['heading' => 'Settings'])

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Application Settings -->
    <div class="mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('settings.edit') }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-200 p-6 block">
                <div class="flex items-start gap-4">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-8 w-8 text-orange-600 flex-shrink-0 mt-1"><path fill-rule="evenodd" d="M7.84 2.66A1.75 1.75 0 0 1 9.5 2h1a1.75 1.75 0 0 1 1.66.66l.39.52c.17.22.44.35.72.35h.6a1.75 1.75 0 0 1 1.65 1.17l.3.9c.09.27.3.48.57.57l.9.3A1.75 1.75 0 0 1 18 8.13v.6c0 .28.13.55.35.72l.52.39a1.75 1.75 0 0 1 .66 1.66v1a1.75 1.75 0 0 1-.66 1.66l-.52.39a.9.9 0 0 0-.35.72v.6a1.75 1.75 0 0 1-1.17 1.65l-.9.3a.9.9 0 0 0-.57.57l-.3.9A1.75 1.75 0 0 1 13.87 18h-.6a.9.9 0 0 0-.72.35l-.39.52A1.75 1.75 0 0 1 10.5 19h-1a1.75 1.75 0 0 1-1.66-.66l-.39-.52a.9.9 0 0 0-.72-.35h-.6a1.75 1.75 0 0 1-1.65-1.17l-.3-.9a.9.9 0 0 0-.57-.57l-.9-.3A1.75 1.75 0 0 1 2 13.87v-.6a.9.9 0 0 0-.35-.72l-.52-.39A1.75 1.75 0 0 1 .47 10.5v-1c0-.55.25-1.07.66-1.4l.52-.39A.9.9 0 0 0 2 7.13v-.6a1.75 1.75 0 0 1 1.17-1.65l.9-.3a.9.9 0 0 0 .57-.57l.3-.9A1.75 1.75 0 0 1 6.13 2h.6c.28 0 .55-.13.72-.35l.39-.52ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" /></svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600">General Settings</h3>
                        <p class="text-sm text-gray-600 mt-1">App preferences and barcode settings</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('settings.mail.edit') }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-200 p-6 block">
                <div class="flex items-start gap-4">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-8 w-8 text-orange-600 flex-shrink-0 mt-1"><path d="M3 4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H3Zm6.622 12H3a1 1 0 0 1-.97-1.243l.822-2.748A1 1 0 0 1 4 11h12a1 1 0 0 1 .97.757l.822 2.748A1 1 0 0 1 17 16h-7.378Z" /></svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600">Mail Settings</h3>
                        <p class="text-sm text-gray-600 mt-1">Mailjet, SMTP, and email notifications</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('backups.index') }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-200 p-6 block">
                <div class="flex items-start gap-4">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-8 w-8 text-orange-600 flex-shrink-0 mt-1"><path d="M10.5 1.5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6.5L10.5 1.5Z" /><path d="M14.5 8h-3V5h-2v3h-3v2h3v3h2v-3h3v-2Z" /></svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600">Backups</h3>
                        <p class="text-sm text-gray-600 mt-1">Database backups to Backblaze B2</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    @if (auth()->user()->isSuperAdmin())
        <!-- Administration -->
        <div class="mb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('users.index') }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-200 p-6 block">
                    <div class="flex items-start gap-4">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-8 w-8 text-orange-600 flex-shrink-0 mt-1"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM5 16.25A4.25 4.25 0 0 1 9.25 12h1.5A4.25 4.25 0 0 1 15 16.25a.75.75 0 0 1-.75.75h-8.5a.75.75 0 0 1-.75-.75Z" /></svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600">Users</h3>
                            <p class="text-sm text-gray-600 mt-1">Manage team members and roles</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif

    <!-- Account Administration -->
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('account.settings') }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-200 p-6 block">
                <div class="flex items-start gap-4">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-8 w-8 text-orange-600 flex-shrink-0 mt-1"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" /></svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600">Account Settings</h3>
                        <p class="text-sm text-gray-600 mt-1">Email, password, and login sessions</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('sessions.active') }}" class="group bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-200 p-6 block">
                <div class="flex items-start gap-4">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-8 w-8 text-orange-600 flex-shrink-0 mt-1"><path d="M10 4a.75.75 0 0 1 .75.75v.5H15a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2h4.25v-.5A.75.75 0 0 1 10 4Z" /></svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600">Active Sessions</h3>
                        <p class="text-sm text-gray-600 mt-1">View and manage login sessions</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

@endsection
