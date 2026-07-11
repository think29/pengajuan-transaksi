<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password'
])]
#[Hidden([
    'password',
    'remember_token'
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Semua pengajuan yang dibuat user.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Semua approval yang dilakukan user.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    /**
     * Semua pembayaran yang diproses Finance.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'finance_id');
    }

    /**
     * Semua aktivitas user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Mengambil user pertama berdasarkan role.
     */
    public static function byRole(string $role): ?self
    {
        return self::role($role)->first();
    }
}