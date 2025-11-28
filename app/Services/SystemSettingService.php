<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use App\Services\ActivityLogService;

class SystemSettingService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getAllSettingsGrouped()
    {
        return Setting::all()->groupBy('group');
    }

    public function updateSettings(array $data)
    {
        $changedSettings = [];

        foreach ($data as $key => $value) {
            // Lọc bỏ các trường hệ thống
            if (in_array($key, ['_token', '_method', 'submit'])) continue;

            // Lấy giá trị cũ để so sánh (nếu có)
            $oldSetting = Setting::where('key', $key)->first();
            $oldValue = $oldSetting ? $oldSetting->value : null;

            // Nếu giá trị không đổi thì bỏ qua (tối ưu performance)
            if ($oldValue === $value) continue;

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => 'general',
                    'type' => 'text'
                ]
            );

            // Lưu lại sự thay đổi để ghi log
            $changedSettings[$key] = [
                'old' => $oldValue,
                'new' => $value
            ];
        }

        // Xóa cache
        Cache::forget('app_settings');

        // Ghi log nếu có thay đổi
        if (!empty($changedSettings)) {
            $this->activityLogService->logAction(
                'Cập nhật cấu hình hệ thống',
                null,
                'updated',
                ['changes' => $changedSettings]
            );
        }
    }
}
