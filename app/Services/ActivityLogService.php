<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Lấy danh sách logs (Đã có)
     */
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

    /**
     * [MỚI] Hàm ghi log thủ công từ Service khác
     * * @param string $description Mô tả hành động (vd: "Cập nhật cấu hình")
     * @param mixed $subject Model bị tác động (vd: Setting)
     * @param string $event Tên sự kiện (created, updated, deleted, login...)
     * @param array $properties Dữ liệu chi tiết thay đổi
     */
    public function logAction($description, $subject = null, $event = 'updated', $properties = [])
    {
        $activityLogger = activity()
            ->event($event)
            ->withProperties($properties);

        // Nếu có subject cụ thể (ví dụ Product)
        if ($subject) {
            $activityLogger = $activityLogger->performedOn($subject);
        }

        $activity = $activityLogger->log($description);

        // Spatie tự động lấy Auth::user() làm causer,
        // nhưng nếu chạy trong Job/Queue thì cần set thủ công nếu muốn.
    }
}
