<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Hiển thị danh sách các hoạt động.
     */
    protected $logService;

    public function __construct(ActivityLogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Hiển thị danh sách các hoạt động.
     */
    public function index(Request $request)
    {
        // Lấy các tham số lọc từ request
        $filters = $request->only(['causer_id', 'event', 'subject_type', 'type']);
        $activities = $this->logService->getLogs($filters);

        return view('admin.activity_logs.index', [
            'activities' => $activities,
            'filters' => $filters
        ]);
    }
}
