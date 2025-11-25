<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    protected $logService;

    public function __construct(ActivityLogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['causer_id', 'event', 'subject_type']);
        $activities = $this->logService->getLogs($filters);

        return view('admin.activity_logs.index', [
            'activities' => $activities,
            'filters' => $filters
        ]);
    }
}
