<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function record(
        string $action,
        string $message,
        ?User $user = null,
        ?Model $entity = null,
        array $metadata = [],
    ): ActivityLog {
        return ActivityLog::query()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => $entity ? $entity::class : null,
            'entity_id' => $entity?->getKey(),
            'message' => $message,
            'metadata' => $metadata ?: null,
        ]);
    }
}
