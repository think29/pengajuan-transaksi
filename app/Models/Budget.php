<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use SoftDeletes;

    /**
     * Mass Assignment
     */
    protected $fillable = [
        'category_id',
        'year',
        'total_budget',
        'used_budget',
    ];

    /**
     * Attribute Casting
     */
    protected $casts = [
        'year' => 'integer',
        'total_budget' => 'decimal:2',
        'used_budget' => 'decimal:2',
    ];

    /**
     * Append Attribute
     */
    protected $appends = [
        'remaining_budget',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    /**
     * Sisa anggaran.
     */
    public function getRemainingBudgetAttribute(): float
    {
        return (float) ($this->total_budget - $this->used_budget);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    */

    /**
     * Budget dimiliki oleh satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}