<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
use App\Services\ProductService;
use App\Http\Requests\SearchProductRequest;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(SearchProductRequest $request, SearchService $searchService, ProductService $productService)
    {
        // Dữ liệu đã được validate, chỉ lấy các trường đã xác thực
        $filters = $request->validated();
        $keyword = $filters['q'] ?? '';

        // 1. Gọi Service với dữ liệu đã được làm sạch
        $data = $searchService->search($keyword, $filters);

        // Xử lý Zero Result: Nếu không có kết quả, lấy thêm sản phẩm gợi ý
        $suggestedProducts = null;
        if ($data->isEmpty()) {
            $suggestedProducts = $productService->getBestSellingSuggestions(4);
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
        // Validation đơn giản cho API gợi ý
        $validated = $request->validate([
            'q' => 'required|string|max:100',
        ]);

        $keyword = $validated['q'];
        $suggestions = $searchService->getSuggestions($keyword);

        $debug = ['module' => 'Search', 'action' => 'Autocomplete', 'term' => $keyword];

        return response()->json([
            'success' => true,
            'data' => $suggestions,
            'debug' => $debug
        ]);
    }
}
