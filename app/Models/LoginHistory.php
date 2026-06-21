<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id', 'ip_address', 'user_agent',
        'device_name', 'location', 'logged_in_at',
        'logged_out_at', 'is_active'
    ];

    protected $casts = [
        'logged_in_at' => 'datetime',
        'logged_out_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logout(): void
    {
        $this->update(['logged_out_at' => now(), 'is_active' => false]);
    }
}
