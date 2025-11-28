<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request, SearchService $searchService, ProductService $productService)
    {
        $keyword = $request->input('q', '');
        $filters = $request->all();

        // 1. Gọi Service
        $data = $searchService->search($keyword, $filters);

        // Xử lý Zero Result: Nếu không có kết quả, lấy thêm sản phẩm gợi ý
        $suggestedProducts = null;
        if ($data->isEmpty()) {
            $suggestedProducts = $productService->listing(['sort' => 'sold_count', 'per_page' => 4]);
        }

        // 2. Tạo debug metadata [cite: 34]
        $debug = [
            'module' => 'Search',
            'action' => 'Index',
            'keyword' => $keyword,
            'count' => $data->total(),
            'user_type' => Auth::check() ? 'User' : 'Guest',
            'filters' => $filters
        ];

        // 3. Hybrid Response (JSON vs View)
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'suggestions' => $suggestedProducts,
                'debug' => $debug
            ]);
        }

        return view('search.index', [
            'products' => $data,
            'keyword' => $keyword,
            'suggestedProducts' => $suggestedProducts,
            'server_debug' => $debug
        ]);
    }

    // API cho Live Search (Autocomplete)
    public function suggestions(Request $request, SearchService $searchService)
    {
        $keyword = $request->input('q');
        $suggestions = $searchService->getSuggestions($keyword);

        $debug = ['module' => 'Search', 'action' => 'Autocomplete', 'term' => $keyword];

        return response()->json([
            'success' => true,
            'data' => $suggestions,
            'debug' => $debug
        ]);
    }
}
