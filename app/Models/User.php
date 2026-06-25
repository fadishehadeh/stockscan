<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'role', 'is_active', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function approvalRequests(): HasMany
    {
        return $this->hasMany(InventoryApprovalRequest::class, 'requester_user_id');
    }

    public function approvedRequests(): HasMany
    {
        return $this->hasMany(InventoryApprovalRequest::class, 'approver_user_id');
    }

    public function isOwner(): bool
    {
        return in_array($this->role, ['owner', 'super_admin'], true);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'owner'], true);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'owner'], true);
    }

    public function isUser(): bool
    {
        return in_array($this->role, ['user', 'staff'], true);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'user'], true);
    }

    public function isPurchaseManager(): bool
    {
        return $this->role === 'purchase_manager';
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canManageBackups(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canViewSessions(): bool
    {
        return true;
    }

    public function canApproveInventoryRequests(): bool
    {
        return $this->isPurchaseManager() || $this->isAdmin();
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_string($roles) ? [$roles] : $roles;

        foreach ($roles as $role) {
            if ($this->matchesRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function matchesRole(string $role): bool
    {
        return match ($role) {
            'owner' => $this->isOwner(),
            'super_admin' => $this->isSuperAdmin(),
            'admin' => $this->isAdmin(),
            'staff', 'user' => $this->isStaff(),
            'purchase_manager' => $this->isPurchaseManager(),
            default => $this->role === $role,
        };
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class, 'created_by');
    }

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }
}
