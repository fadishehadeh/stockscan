<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\ActivityLogService;
use App\Services\MailjetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly MailjetService $mailjetService
    ) {
    }

    public function edit(): View
    {
        return view('settings.edit', [
            'settings' => AppSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'scanner_mode' => ['required', 'in:keyboard_wedge'],
            'auto_submit_on_enter' => ['nullable', 'boolean'],
            'default_post_scan_behavior' => ['required', 'in:open_product_actions'],
            'default_stock_action' => ['required', 'in:out,in,adjustment'],
            'label_size_default' => ['required', 'in:small,medium,large'],
            'barcode_prefix' => ['nullable', 'string', 'max:8', 'regex:/^\d*$/'],
            'barcode_random_length' => ['required', 'integer', 'min:6', 'max:16'],
        ]);

        $data['auto_submit_on_enter'] = $request->boolean('auto_submit_on_enter');

        AppSetting::current()->update($data);

        $this->activityLogService->record('settings.updated', 'Updated application settings.', $request->user(), null, $data);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function editMailSettings(): View
    {
        $settings = AppSetting::current();
        return view('settings.mail', [
            'settings' => $settings,
            'mailProviders' => ['mailjet' => 'Mailjet', 'smtp' => 'SMTP'],
        ]);
    }

    public function updateMailSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mail_provider' => ['required', 'in:mailjet,smtp'],
            'mailjet_api_key' => ['nullable', 'string'],
            'mailjet_api_secret' => ['nullable', 'string'],
            'mailjet_from_email' => ['nullable', 'email'],
            'mailjet_from_name' => ['nullable', 'string', 'max:255'],
            'smtp_host' => ['nullable', 'string'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string'],
            'smtp_password' => ['nullable', 'string'],
            'notifications_enabled' => ['nullable', 'boolean'],
            'test_email_address' => ['nullable', 'email'],
        ]);

        $data['notifications_enabled'] = $request->boolean('notifications_enabled');

        AppSetting::current()->update($data);

        $this->activityLogService->record('settings.mail_updated', 'Updated mail settings.', $request->user(), null, ['provider' => $data['mail_provider']]);

        return back()->with('success', 'Mail settings updated successfully.');
    }

    public function testMailConnection(): RedirectResponse
    {
        try {
            $connected = $this->mailjetService->testConnection();

            if ($connected) {
                return back()->with('success', 'Mail connection test passed!');
            } else {
                return back()->with('error', 'Mail connection test failed. Please check your credentials.');
            }
        } catch (\Exception $e) {
            return back()->with('error', "Connection test failed: {$e->getMessage()}");
        }
    }
}
