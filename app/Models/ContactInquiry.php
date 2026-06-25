<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'email',
    'phone',
    'company',
    'business_type',
    'message',
    'recipient_email',
    'email_delivered',
    'delivery_error',
    'submitted_at',
    'ip_address',
    'user_agent',
])]
class ContactInquiry extends Model
{
    protected function casts(): array
    {
        return [
            'email_delivered' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }
}
