<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactInquiryController extends Controller
{
    public function index(Request $request): View
    {
        $inquiries = ContactInquiry::query()
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = $request->string('status')->toString();

                return match ($status) {
                    'delivered' => $query->where('email_delivered', true),
                    'failed' => $query->where('email_delivered', false),
                    default => $query,
                };
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = trim($request->string('search')->toString());

                $query->where(function ($builder) use ($term) {
                    $builder
                        ->where('name', 'like', '%' . $term . '%')
                        ->orWhere('email', 'like', '%' . $term . '%')
                        ->orWhere('company', 'like', '%' . $term . '%')
                        ->orWhere('message', 'like', '%' . $term . '%');
                });
            })
            ->when($request->filled('from'), fn ($query) => $query->whereDate('submitted_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('submitted_at', '<=', $request->date('to')))
            ->latest('submitted_at')
            ->paginate(20)
            ->withQueryString();

        return view('contact-inquiries.index', [
            'inquiries' => $inquiries,
            'summary' => [
                'total' => ContactInquiry::query()->count(),
                'delivered' => ContactInquiry::query()->where('email_delivered', true)->count(),
                'failed' => ContactInquiry::query()->where('email_delivered', false)->count(),
            ],
        ]);
    }
}
