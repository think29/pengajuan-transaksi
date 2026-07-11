<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Workflow Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_WAITING_SPV = 'waiting_spv';

    public const STATUS_WAITING_MANAGER = 'waiting_manager';

    public const STATUS_WAITING_DIRECTOR = 'waiting_director';

    public const STATUS_WAITING_FINANCE = 'waiting_finance';

    public const STATUS_PAID = 'paid';

    public const STATUS_REJECTED = 'rejected';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'submission_number',
        'user_id',
        'category_id',
        'budget_id',
        'submission_date',
        'amount',
        'description',
        'attachment',
        'status',
        'current_approval',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'submission_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Staff Pengaju
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kategori Pengajuan
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Budget yang digunakan pada pengajuan.
     */
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Riwayat Approval
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class)
            ->orderBy('step');
    }

    public function currentApproval(): HasOne
    {
        return $this->hasOne(Approval::class)
            ->where('is_current', true);
    }

    /**
     * Lampiran Tambahan
     */
    public function submissionFiles(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    /**
     * Data Pembayaran
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */


    /**
     * Total Budget
     */
    public function getTotalBudgetAttribute(): float
    {
        return $this->budget?->total_budget ?? 0;
    }

    /**
     * Budget Terpakai
     */
    public function getUsedBudgetAttribute(): float
    {
        return $this->budget?->used_budget ?? 0;
    }

    /**
     * Sisa Budget
     */
    public function getRemainingBudgetAttribute(): float
    {
        return $this->budget?->remaining_budget ?? 0;
    }

    /**
     * Persentase Budget Terpakai
     */
    public function getBudgetProgressAttribute(): float
    {
        if ($this->total_budget <= 0) {
            return 0;
        }

        return round(
            ($this->used_budget / $this->total_budget) * 100,
            2
        );
    }

    /**
     * Cek Budget Masih Cukup
     */
    public function isBudgetAvailable(): bool
    {
        return $this->remaining_budget >= $this->amount;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeWaitingApproval(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_WAITING_SPV,
            self::STATUS_WAITING_MANAGER,
            self::STATUS_WAITING_DIRECTOR,
            self::STATUS_WAITING_FINANCE,
        ]);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helper
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isWaitingSpv(): bool
    {
        return $this->status === self::STATUS_WAITING_SPV;
    }

    public function isWaitingManager(): bool
    {
        return $this->status === self::STATUS_WAITING_MANAGER;
    }

    public function isWaitingDirector(): bool
    {
        return $this->status === self::STATUS_WAITING_DIRECTOR;
    }

    public function isWaitingFinance(): bool
    {
        return $this->status === self::STATUS_WAITING_FINANCE;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isWaitingApproval(): bool
    {
        return in_array($this->status, [
            self::STATUS_WAITING_SPV,
            self::STATUS_WAITING_MANAGER,
            self::STATUS_WAITING_DIRECTOR,
            self::STATUS_WAITING_FINANCE,
        ]);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_PAID,
            self::STATUS_REJECTED,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Mengubah status workflow.
     */
    public function changeStatus(
        string $status,
        ?string $currentApproval = null
    ): self {
        $this->update([
            'status' => $status,
            'current_approval' => $currentApproval,
        ]);

        return $this->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Presentation Helper
    |--------------------------------------------------------------------------
    */

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            self::STATUS_DRAFT => 'Draft',

            self::STATUS_SUBMITTED => 'Submitted',

            self::STATUS_WAITING_SPV => 'Waiting SPV',

            self::STATUS_WAITING_MANAGER => 'Waiting Manager',

            self::STATUS_WAITING_DIRECTOR => 'Waiting Director',

            self::STATUS_WAITING_FINANCE => 'Waiting Finance',

            self::STATUS_PAID => 'Paid',

            self::STATUS_REJECTED => 'Rejected',

            default => '-',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Generate Nomor Pengajuan
     *
     * Format:
     * SUB-20260709-0001
     */
    public static function generateSubmissionNumber(): string
    {
        $prefix = 'SUB-' . now()->format('Ymd');

        $last = self::withTrashed()
            ->whereDate('created_at', today())
            ->latest('id')
            ->first();

        $number = $last
            ? ((int) substr($last->submission_number, -4)) + 1
            : 1;

        return $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}