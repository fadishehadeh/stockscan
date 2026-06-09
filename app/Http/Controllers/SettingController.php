<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly ActivityLogService $activityLogService)
    {
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
}
