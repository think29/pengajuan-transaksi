<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;

    /**
     * Mass Assignment
     */
    protected $fillable = [
        'name',
        'is_po_product',
        'description',
    ];

    /**
     * Attribute Casting
     */
    protected $casts = [
        'is_po_product' => 'boolean',
    ];

    /**
     * Relationship
     * ----------------------------------------------------------
     */

    /**
     * Satu kategori memiliki banyak budget.
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Satu kategori memiliki banyak pengajuan.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}