<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFile extends Model
{
    /**
     * Mass Assignment
     */
    protected $fillable = [
        'submission_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    */

    /**
     * Submission pemilik file.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}