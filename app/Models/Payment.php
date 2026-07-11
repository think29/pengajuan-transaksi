<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Payment Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'submission_id',
        'finance_id',
        'payment_amount',
        'payment_date',
        'status',
        'payment_method',
        'reference_number',
        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function finance(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finance_id');
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

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helper
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow Helper
    |--------------------------------------------------------------------------
    */

    public function markPaid(
        User $finance,
        ?string $referenceNumber = null,
        ?string $paymentMethod = null,
        ?string $notes = null,
    ): self {

        $this->update([

            'finance_id' => $finance->id,

            'status' => self::STATUS_PAID,

            'payment_date' => now(),

            'reference_number' => $referenceNumber,

            'payment_method' => $paymentMethod,

            'notes' => $notes,

        ]);

        return $this->refresh();
    }
}