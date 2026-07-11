<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Approval Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /*
    |--------------------------------------------------------------------------
    | Workflow Role
    |--------------------------------------------------------------------------
    */

    public const ROLE_SPV = 'SPV';

    public const ROLE_MANAGER = 'Manager';

    public const ROLE_DIRECTOR = 'Director';

    public const ROLE_FINANCE = 'Finance';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'submission_id',
        'approver_id',
        'step',
        'role',
        'status',
        'is_current',
        'notes',
        'action_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
    'action_at' => 'datetime',
    'is_current' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Submission yang di-approval.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * User yang melakukan approval.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isSpv(): bool
    {
        return $this->role === self::ROLE_SPV;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isDirector(): bool
    {
        return $this->role === self::ROLE_DIRECTOR;
    }

    public function isFinance(): bool
    {
        return $this->role === self::ROLE_FINANCE;
    }

    /**
     * Mengecek apakah user berhak melakukan approval.
     */
    public function canApprove(User $user): bool
    {
        return $this->approver_id === $user->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow Helper
    |--------------------------------------------------------------------------
    */

    public function approve(?string $note = null): self
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'notes' => $note,
            'action_at' => now(),
            'is_current' => false,
        ]);

        return $this->refresh();
    }

    public function reject(?string $note = null): self
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'notes' => $note,
            'action_at' => now(),
            'is_current' => false,
        ]);

        return $this->refresh();
    }

    public function activate(): self
    {
        $this->update([
            'is_current' => true,
        ]);

        return $this->refresh();
    }
    
    public function deactivate(): self
    {
        $this->update([
            'is_current' => false,
        ]);

        return $this->refresh();
    }
}