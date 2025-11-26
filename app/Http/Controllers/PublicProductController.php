<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Trang danh sách sản phẩm theo danh mục
     */
    public function category(Request $request, $slug)
    {
        $filters = $request->only(['price_min', 'price_max', 'sort']);

        $data = $this->productService->getProductsByCategory($slug, 12, $filters);

        // Chuẩn bị dữ liệu response
        $response = [
            'category' => $data['category'],
            'products' => $data['products']
        ];

        // Hybrid Response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $response,
                'debug' => ['module' => 'Category', 'slug' => $slug]
            ]);
        }

        return view('products.category', $response);
    }

    /**
     * Trang chi tiết sản phẩm
     */
    public function show(Request $request, $slug)
    {
        $product = $this->productService->findActiveBySlug($slug);

        // Format dữ liệu để frontend dễ dùng
        $productData = $product->toArray();

        // Thêm logic "Related Products" (Sản phẩm liên quan - cùng danh mục)
        $related = \App\Models\Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        $response = [
            'product' => $productData,
            'related' => $related
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $response,
                'debug' => ['module' => 'ProductDetail', 'slug' => $slug]
            ]);
        }

        return view('products.show', $response);
    }
}
