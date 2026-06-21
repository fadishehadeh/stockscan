@extends('layouts.app', ['title' => 'Settings · StockScan', 'heading' => 'Settings'])

@section('content')
    <section class="grid gap-6 xl:grid-cols-[1fr_0.95fr]">
        <article class="panel">
            <div class="panel-header">
                <div class="max-w-2xl">
                    <p class="eyebrow">Scanner Setup</p>
                    <h3 class="panel-title mt-2">Scanner and barcode settings</h3>
                    <p class="panel-subtitle">These settings apply to the hosted web app. The scanner remains connected to the local computer and sends barcode text through the browser.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" class="mt-6 space-y-5" data-prevent-double-submit>
                @csrf
                @method('PUT')

                <div class="product-form-grid">
                    <div>
                        <label class="label" for="scanner_mode">Scanner Mode</label>
                        <select id="scanner_mode" name="scanner_mode" class="input">
                            <option value="keyboard_wedge" @selected($settings->scanner_mode === 'keyboard_wedge')>USB Keyboard Wedge</option>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="default_stock_action">Default Stock Action</label>
                        <select id="default_stock_action" name="default_stock_action" class="input">
                            <option value="out" @selected($settings->default_stock_action === 'out')>Stock Out</option>
                            <option value="in" @selected($settings->default_stock_action === 'in')>Stock In</option>
                            <option value="adjustment" @selected($settings->default_stock_action === 'adjustment')>Adjustment</option>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="default_post_scan_behavior">Post-Scan Behavior</label>
                        <select id="default_post_scan_behavior" name="default_post_scan_behavior" class="input">
                            <option value="open_product_actions" @selected($settings->default_post_scan_behavior === 'open_product_actions')>Open Product Actions</option>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="label_size_default">Default Label Size</label>
                        <select id="label_size_default" name="label_size_default" class="input">
                            <option value="small" @selected($settings->label_size_default === 'small')>Small</option>
                            <option value="medium" @selected($settings->label_size_default === 'medium')>Medium</option>
                            <option value="large" @selected($settings->label_size_default === 'large')>Large</option>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="barcode_prefix">Barcode Prefix</label>
                        <input id="barcode_prefix" name="barcode_prefix" value="{{ old('barcode_prefix', $settings->barcode_prefix) }}" class="input" placeholder="Optional digits only">
                    </div>
                    <div>
                        <label class="label" for="barcode_random_length">Random Barcode Digits</label>
                        <input id="barcode_random_length" name="barcode_random_length" type="number" min="6" max="16" value="{{ old('barcode_random_length', $settings->barcode_random_length) }}" class="input">
                    </div>
                </div>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                    <input type="checkbox" name="auto_submit_on_enter" value="1" @checked($settings->auto_submit_on_enter) class="h-4 w-4 rounded border-slate-300 text-sky-600">
                    Auto-submit the scan page when the scanner sends Enter after the barcode
                </label>

                <button class="btn btn-primary" data-submit-label="Save Settings">Save Settings</button>
            </form>
        </article>

        <article class="space-y-6">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Connection Guide</p>
                        <h3 class="panel-title mt-2">How to connect the scanner</h3>
                    </div>
                </div>
                <div class="surface-list mt-5">
                    <div class="surface-item">1. Plug the barcode scanner into the local computer by USB.</div>
                    <div class="surface-item">2. Configure the scanner in keyboard mode, not serial-only mode.</div>
                    <div class="surface-item">3. Open the online StockScan scan page in the browser.</div>
                    <div class="surface-item">4. Click the barcode field once, then scan. The device types into that input like a keyboard.</div>
                    <div class="surface-item">5. For the fastest workflow, configure the scanner to send Enter after each scan.</div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Device Test</p>
                        <h3 class="panel-title mt-2">Scanner test input</h3>
                        <p class="panel-subtitle">Click in the field below and scan a barcode to confirm the local device is sending text into the browser.</p>
                    </div>
                </div>
                <div class="mt-5">
                    <label class="label" for="scanner_test">Test Input</label>
                    <input id="scanner_test" class="input input-scan" placeholder="Scan here to test the local scanner" data-autofocus>
                    <p class="label-hint">If characters appear here, the scanner is connected correctly. The PHP server does not connect to the scanner directly.</p>
                </div>
            </section>
        </article>
    </section>
@endsection
