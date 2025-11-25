<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;

class ActivityLogService
{
    public function getLogs($filters = [], $perPage = 20)
    {
        $query = Activity::with('causer')->orderByDesc('created_at');

        if (!empty($filters['causer_id'])) {
            $query->where('causer_id', $filters['causer_id']);
        }

        if (!empty($filters['subject_type'])) {
            $query->where('subject_type', 'like', '%' . $filters['subject_type'] . '%');
        }

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
