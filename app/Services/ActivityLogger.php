<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogger
{
    public static function record(?User $user, string $action, object $subject, array $metadata = []): void
    {
        ActivityLog::create(['user_id' => $user?->id, 'action' => $action, 'subject_type' => $subject::class, 'subject_id' => $subject->id ?? null, 'metadata' => $metadata]);
    }
}
