<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Submission;

class ActivityLogService
{
    public static function log(
        string $activity,
        ?Submission $submission = null,
        ?string $description = null,
    ): void {

        if (! auth()->check()) {
            return;
        }

        ActivityLog::create([
            'user_id'       => auth()->id(),
            'submission_id' => $submission?->id,
            'activity'      => $activity,
            'description'   => $description,
            'ip_address'    => request()->ip(),
        ]);
    }
}