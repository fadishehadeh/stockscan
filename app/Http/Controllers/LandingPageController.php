<?php

namespace App\Http\Controllers;

use App\Mail\ContactInquiryMail;
use App\Models\ContactInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('dashboard');
        }

        return view('marketing.home', [
            'meta' => [
                'title' => 'StockScan | Barcode Inventory Software for Warehouses, Retail, and Field Teams',
                'description' => 'StockScan is a barcode-ready inventory management app for teams that need fast stock control, product tracking, low-stock alerts, printable labels, reports, and secure multi-user access.',
                'keywords' => 'inventory software, barcode inventory system, stock management app, PHP inventory system, MySQL inventory software, warehouse inventory tracking, retail stock control, inventory software Lebanon, barcode scanner inventory app',
                'canonical' => url('/'),
                'geo' => [
                    'placename' => 'Beirut',
                    'region' => 'LB-BA',
                    'position' => '33.8938;35.5018',
                ],
            ],
            'faqItems' => [
                [
                    'question' => 'How does StockScan work with a barcode scanner?',
                    'answer' => 'StockScan works with standard USB barcode scanners in keyboard-wedge mode. The scanner types into the focused browser field, so no special server-side hardware driver is needed.',
                ],
                [
                    'question' => 'Can StockScan be hosted online while scanners stay local?',
                    'answer' => 'Yes. The web app can be hosted online while each local scanner sends scanned codes through the user browser to the live system.',
                ],
                [
                    'question' => 'Does StockScan generate barcodes and printable labels?',
                    'answer' => 'Yes. Products can receive generated internal barcodes, and the system supports printable Code 128 style labels for sticker printing.',
                ],
                [
                    'question' => 'Who is StockScan built for?',
                    'answer' => 'It fits retail stores, stockrooms, warehouses, production teams, and multi-user operations that want a simple PHP and MySQL inventory tool with barcode workflows.',
                ],
            ],
            'contactRecipient' => config('mail.contact_to'),
            'softwareSchemaJson' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'SoftwareApplication',
                'name' => 'StockScan',
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web',
                'description' => 'StockScan is a barcode-ready inventory management app for teams that need fast stock control, product tracking, low-stock alerts, printable labels, reports, and secure multi-user access.',
                'url' => url('/'),
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '0',
                    'priceCurrency' => 'USD',
                ],
                'areaServed' => ['Lebanon', 'Middle East', 'Worldwide'],
                'featureList' => [
                    'Barcode scanning through browser input',
                    'Auto-generated product barcodes',
                    'Low-stock alerts and inventory dashboard',
                    'Product images and cost tracking',
                    'Printable barcode sticker labels',
                    'Role-based multi-user access',
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'faqSchemaJson' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn (array $item) => [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['answer'],
                    ],
                ], [
                    [
                        'question' => 'How does StockScan work with a barcode scanner?',
                        'answer' => 'StockScan works with standard USB barcode scanners in keyboard-wedge mode. The scanner types into the focused browser field, so no special server-side hardware driver is needed.',
                    ],
                    [
                        'question' => 'Can StockScan be hosted online while scanners stay local?',
                        'answer' => 'Yes. The web app can be hosted online while each local scanner sends scanned codes through the user browser to the live system.',
                    ],
                    [
                        'question' => 'Does StockScan generate barcodes and printable labels?',
                        'answer' => 'Yes. Products can receive generated internal barcodes, and the system supports printable Code 128 style labels for sticker printing.',
                    ],
                    [
                        'question' => 'Who is StockScan built for?',
                        'answer' => 'It fits retail stores, stockrooms, warehouses, production teams, and multi-user operations that want a simple PHP and MySQL inventory tool with barcode workflows.',
                    ],
                ]),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ]);
    }

    public function contact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:60'],
            'company' => ['nullable', 'string', 'max:140'],
            'business_type' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
        ]);

        $payload = array_merge($data, [
            'submitted_at' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $recipient = config('mail.contact_to');
        $delivered = false;
        $deliveryError = null;

        $inquiry = ContactInquiry::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'business_type' => $data['business_type'] ?? null,
            'message' => $data['message'],
            'recipient_email' => $recipient,
            'submitted_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($recipient) {
            try {
                Mail::to($recipient)->send(new ContactInquiryMail($payload));
                $delivered = true;
            } catch (\Throwable $exception) {
                $deliveryError = $exception->getMessage();

                Log::warning('landing.contact_mail_failed', [
                    'recipient' => $recipient,
                    'error' => $deliveryError,
                    'payload' => $payload,
                ]);
            }
        }

        $inquiry->forceFill([
            'email_delivered' => $delivered,
            'delivery_error' => $deliveryError,
        ])->save();

        Log::info('landing.contact_submitted', [
            'delivered' => $delivered,
            'recipient' => $recipient,
            'contact_inquiry_id' => $inquiry->id,
            'payload' => $payload,
        ]);

        return back()->with('success', $delivered
            ? 'Thanks. Your message was sent successfully. We will contact you shortly.'
            : 'Thanks. Your message was received and saved. We will contact you shortly.');
    }
}
