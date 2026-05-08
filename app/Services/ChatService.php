<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ChatService
{
    /**
     * Hàm xử lý tìm kiếm sản phẩm dựa trên câu hỏi của người dùng từ Chatbot.
     *
     * @param string $query
     * @return array
     */
    public function searchProducts(string $query): array
    {
        // 1. Chuẩn hóa từ khóa
        $query = Str::lower($query);
        $replacements = [
            'ip' => 'iphone',
            'ip15' => 'iphone 15',
            'samsung' => 'samsung',
            'ss' => 'samsung',
            'xiaomi' => 'xiaomi',
            'xm' => 'xiaomi',
            'dt' => 'điện thoại',
            'đt' => 'điện thoại',
            'lap' => 'laptop',
            'so sanh' => 'so sánh',
            'vs' => 'so sánh',
        ];

        foreach ($replacements as $short => $full) {
            $query = preg_replace("/\b$short\b/u", $full, $query);
        }

        // 2. PHÁT HIỆN THƯƠNG HIỆU & DANH MỤC
        // Định nghĩa các từ khóa "Kích hoạt" việc lấy danh sách rộng hơn
        $brands = ['iphone', 'samsung', 'oppo', 'xiaomi', 'dell', 'hp', 'asus', 'macbook', 'ipad'];
        $categories = ['điện thoại', 'laptop', 'đồng hồ', 'phụ kiện'];

        $foundBrands = [];
        foreach ($brands as $brand) {
            if (Str::contains($query, $brand)) {
                $foundBrands[] = $brand;
            }
        }

        // 3. Truy vấn Database thông minh hơn
        $products = Product::query()
            ->where('is_active', true) // Chỉ lấy sản phẩm đang bán
            ->where(function ($q) use ($categories, $query, $foundBrands) {

                // A. Tìm kiếm chính xác theo tên (Logic cũ nhưng tối ưu hơn)
                $keywords = explode(' ', $query);
                $stopwords = ['tôi', 'muốn', 'cần', 'tìm', 'mua', 'giá', 'bao', 'nhiêu', 'shop', 'có', 'không', 'với', 'và', 'so', 'sánh', 'dòng', 'loại', 'nào'];
                $keywords = array_diff($keywords, $stopwords);

                // Nhóm 1: Tìm theo từ khóa cụ thể (ví dụ: "iphone 15")
                $q->orWhere(function ($subQ) use ($keywords) {
                    foreach ($keywords as $word) {
                        if (strlen($word) > 1) {
                            $subQ->where('name', 'like', "%{$word}%");
                        }
                    }
                });

                // B. Tìm kiếm mở rộng theo Thương hiệu
                // Nếu câu hỏi có chữ "samsung", lấy thêm top 5 sản phẩm samsung mới nhất
                if (!empty($foundBrands)) {
                    foreach ($foundBrands as $categories) {
                        $q->orWhere('name', 'like', "%{$categories}%");
                    }
                }
            })
            ->select(['id', 'name', 'price', 'quantity', 'image', 'slug', 'description', 'short_description']) // Lấy thêm description để AI so sánh
            ->limit(10) // Tăng giới hạn lên để AI có đủ dữ liệu so sánh
            ->get();

        // 4. Format dữ liệu trả về cho AI
        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format((float) $product->price) . ' VNĐ',
                'quantity' => $product->quantity,
                'specs' => Str::limit(strip_tags($product->short_description . ' ' . $product->description), 500),
                'image' => filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image),
                'slug' => $product->slug,
            ];
        })->toArray();
    }

    public function getAnswerFromAI($userMessage, $chatHistory, $productsContext)
    {
        // Cấu hình địa chỉ Server Python (Localhost port 5000)
        $pythonUrl = 'http://127.0.0.1:5000/process-chat';
        try {
            // Gửi POST Request sang Python
            $response = Http::timeout(30)->post($pythonUrl, [
                'message' => $userMessage,
                'history' => $chatHistory,
                'products_context' => $productsContext // Gửi kèm danh sách SP tìm được từ searchProducts
            ]);
            if ($response->successful()) {
                return $response->json(); // Trả về mảng kết quả từ Python
            }
            return [
                'text' => 'Xin lỗi, hệ thống AI đang quá tải.',
                'order_status' => 'browsing'
            ];
        } catch (\Exception $e) {
            Log::error("Lỗi kết nối Python AI: " . $e->getMessage());
            return [
                'text' => 'Hệ thống đang bảo trì, vui lòng thử lại sau.',
                'order_status' => 'browsing'
            ];
        }
    }
}
