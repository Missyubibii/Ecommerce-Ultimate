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
        // Xử lý upload ảnh riêng nếu có (ví dụ logo) - logic đơn giản hóa ở đây
        $data = $request->except(['_token', '_method']);

        $this->settingService->updateSettings($data);

        return back()->with('success', 'Cập nhật cấu hình thành công.');
    }
}
