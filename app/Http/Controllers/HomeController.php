<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy Banner (Đã fix model có image_url)
        $banners = Banner::where('position', 'main_slider')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // 2. Lấy Danh mục Gốc kèm danh mục con
        $rootCategories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        // 3. Xử lý Sections: Lấy sản phẩm từ cả danh mục CHA và CON
        $sections = $rootCategories->map(function ($cat) {
            // Lấy ID của danh mục cha hiện tại + tất cả ID danh mục con
            $categoryIds = $cat->children->pluck('id')->push($cat->id);

            // Truy vấn sản phẩm thuộc bất kỳ ID nào trong danh sách trên
            $products = Product::whereIn('category_id', $categoryIds)
                ->where('status', 'active') // Đảm bảo sản phẩm đang Active
                ->latest()
                ->take(8)
                ->get()
                ->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => $p->price,
                        'cost_price' => $p->cost_price,
                        'image_url' => $p->image_url, 
                        'detail_url' => route('product.show', $p->slug)
                    ];
                });

            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'url' => route('category.show', $cat->slug),
                'products' => $products
            ];
        });

        // 4. Menu Tree (Dùng lại $rootCategories cho tối ưu)
        $menuTree = $rootCategories->map(function($cat){
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'children' => $cat->children
            ];
        });

        // --- TRẢ VỀ JSON NẾU LÀ AJAX (ALPINE.JS GỌI) ---
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'banners' => $banners,
                    'sections' => $sections,
                    'menu' => $menuTree
                ]
            ]);
        }

        // Trả về View lần đầu load trang
        return view('home');
    }
}
