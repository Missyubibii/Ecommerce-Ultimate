<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            // Placeholder cho Orders/Products sau này
            'total_orders' => 0,
            'revenue' => 0,
        ];

        $debug = [
            'module' => 'AdminDashboard',
            'action' => 'Index',
            'stats' => $stats
        ];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $stats, 'debug' => $debug]);
        }

        return view('admin.dashboard', ['stats' => $stats, 'server_debug' => $debug]);
    }
}
