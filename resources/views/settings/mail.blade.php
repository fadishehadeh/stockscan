@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">
                    Mail Settings
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Configure email delivery settings for your StockScan instance.
                </p>
            </div>

            @if ($errors->any())
                <div class="p-6 bg-red-50 border-l-4 border-red-400">
                    <h3 class="text-sm font-medium text-red-800">Validation errors:</h3>
                    <ul class="mt-2 space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="p-6 bg-green-50 border-l-4 border-green-400">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="p-6 bg-red-50 border-l-4 border-red-400">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('settings.mail.update') }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="mail_provider" class="block text-sm font-medium text-gray-700">
                        Mail Provider
                    </label>
                    <select
                        id="mail_provider"
                        name="mail_provider"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                        onchange="toggleMailProviderFields()"
                    >
                        @foreach ($mailProviders as $value => $label)
                            <option value="{{ $value }}" {{ $settings->mail_provider === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('mail_provider')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mailjet Settings -->
                <div id="mailjet-settings" class="space-y-6 border-t border-gray-200 pt-6" style="display: {{ $settings->mail_provider === 'mailjet' ? 'block' : 'none' }};">
                    <h3 class="text-lg font-medium text-gray-900">Mailjet Configuration</h3>

                    <div>
                        <label for="mailjet_api_key" class="block text-sm font-medium text-gray-700">
                            API Key
                        </label>
                        <input
                            id="mailjet_api_key"
                            name="mailjet_api_key"
                            type="password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            value="{{ $settings->mailjet_api_key ?? '' }}"
                            autocomplete="off"
                        >
                        @error('mailjet_api_key')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep existing key</p>
                    </div>

                    <div>
                        <label for="mailjet_api_secret" class="block text-sm font-medium text-gray-700">
                            API Secret
                        </label>
                        <input
                            id="mailjet_api_secret"
                            name="mailjet_api_secret"
                            type="password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            value="{{ $settings->mailjet_api_secret ?? '' }}"
                            autocomplete="off"
                        >
                        @error('mailjet_api_secret')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep existing secret</p>
                    </div>

                    <div>
                        <label for="mailjet_from_email" class="block text-sm font-medium text-gray-700">
                            From Email Address
                        </label>
                        <input
                            id="mailjet_from_email"
                            name="mailjet_from_email"
                            type="email"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            value="{{ $settings->mailjet_from_email ?? '' }}"
                        >
                        @error('mailjet_from_email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mailjet_from_name" class="block text-sm font-medium text-gray-700">
                            From Name
                        </label>
                        <input
                            id="mailjet_from_name"
                            name="mailjet_from_name"
                            type="text"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            value="{{ $settings->mailjet_from_name ?? 'StockScan' }}"
                        >
                        @error('mailjet_from_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- SMTP Settings -->
                <div id="smtp-settings" class="space-y-6 border-t border-gray-200 pt-6" style="display: {{ $settings->mail_provider === 'smtp' ? 'block' : 'none' }};">
                    <h3 class="text-lg font-medium text-gray-900">SMTP Configuration</h3>

                    <div>
                        <label for="smtp_host" class="block text-sm font-medium text-gray-700">
                            Host
                        </label>
                        <input
                            id="smtp_host"
                            name="smtp_host"
                            type="text"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            value="{{ $settings->smtp_host ?? '' }}"
                        >
                        @error('smtp_host')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="smtp_port" class="block text-sm font-medium text-gray-700">
                                Port
                            </label>
                            <input
                                id="smtp_port"
                                name="smtp_port"
                                type="number"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                value="{{ $settings->smtp_port ?? '587' }}"
                            >
                            @error('smtp_port')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="smtp_username" class="block text-sm font-medium text-gray-700">
                                Username
                            </label>
                            <input
                                id="smtp_username"
                                name="smtp_username"
                                type="text"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                value="{{ $settings->smtp_username ?? '' }}"
                            >
                            @error('smtp_username')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="smtp_password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <input
                            id="smtp_password"
                            name="smtp_password"
                            type="password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            value="{{ $settings->smtp_password ?? '' }}"
                            autocomplete="off"
                        >
                        @error('smtp_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep existing password</p>
                    </div>
                </div>

                <!-- General Email Settings -->
                <div class="border-t border-gray-200 pt-6 space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Email Settings</h3>

                    <div>
                        <label for="test_email_address" class="block text-sm font-medium text-gray-700">
                            Test Email Address
                        </label>
                        <input
                            id="test_email_address"
                            name="test_email_address"
                            type="email"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            value="{{ $settings->test_email_address ?? '' }}"
                        >
                        @error('test_email_address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Used for testing mail configuration</p>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="notifications_enabled"
                                value="1"
                                {{ $settings->notifications_enabled ? 'checked' : '' }}
                                class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            >
                            <span class="ml-3 text-sm text-gray-700">Enable email notifications</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center space-x-4 border-t border-gray-200 pt-6">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700"
                    >
                        Save Settings
                    </button>

                    <form method="POST" action="{{ route('settings.mail.test') }}" style="display:inline;">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Test Connection
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleMailProviderFields() {
    const provider = document.getElementById('mail_provider').value;
    document.getElementById('mailjet-settings').style.display = provider === 'mailjet' ? 'block' : 'none';
    document.getElementById('smtp-settings').style.display = provider === 'smtp' ? 'block' : 'none';
}
</script>
@endsection
