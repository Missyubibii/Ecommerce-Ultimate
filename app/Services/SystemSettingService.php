<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SystemSettingService
{
    /**
     * Lấy tất cả settings, nhóm theo group
     */
    public function getAllSettingsGrouped()
    {
        return Setting::all()->groupBy('group');
    }

    /**
     * Cập nhật hàng loạt settings
     */
    public function updateSettings(array $data)
    {
        foreach ($data as $key => $value) {
            // Bỏ qua token và method
            if (in_array($key, ['_token', '_method'])) continue;

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear cache nếu có dùng cache setting
        Cache::forget('app_settings');
    }
}
