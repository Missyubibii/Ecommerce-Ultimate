<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchLog;
use App\Models\SearchTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchReportController extends Controller
{
    public function index()
    {
        // 1. Thống kê từ khóa phổ biến nhất (Trending)
        $topKeywords = SearchTerm::orderByDesc('hits')
            ->limit(10)
            ->get();

        // 2. Thống kê "Zero Results" (QUAN TRỌNG NHẤT)
        $zeroResultKeywords = SearchLog::select('keyword', DB::raw('count(*) as count'))
            ->where('results_count', 0)
            ->groupBy('keyword')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // 3. Lịch sử tìm kiếm gần đây
        $recentSearches = SearchLog::with('user') 
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.search_reports.index', compact('topKeywords', 'zeroResultKeywords', 'recentSearches'));
    }
}
