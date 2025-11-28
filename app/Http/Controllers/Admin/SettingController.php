<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemSettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SystemSettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        $settingsGrouped = $this->settingService->getAllSettingsGrouped();
        return view('admin.settings.index', ['settingsGrouped' => $settingsGrouped]);
    }

    public function update(Request $request)
    {
        try {
            // Loại bỏ token và method trước khi gửi sang service
            $data = $request->except(['_token', '_method']);

            // Gọi service để xử lý data
            $this->settingService->updateSettings($data);

            return back()->with('success', 'Cập nhật cấu hình thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    }
}
