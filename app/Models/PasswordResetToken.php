<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['email', 'token', 'created_at'])]
class PasswordResetToken extends Model
{
    public $timestamps = false;

    protected $table = 'password_reset_tokens';

    protected $fillable = ['email', 'token', 'created_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
