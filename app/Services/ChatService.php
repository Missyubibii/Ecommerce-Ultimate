<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChatService
{
    /**
     * Search products based on user query.
     *
     * @param string $query
     * @return array
     */
    public function searchProducts(string $query): array
    {
        // 1. Chuẩn hóa từ khóa lóng (Mapping)
        $query = Str::lower($query);
        $replacements = [
            'ip' => 'iphone',
            'ip15' => 'iphone 15',
            'samsung' => 'samsung',
            'dt' => 'điện thoại',
            'đt' => 'điện thoại',
            'qa' => 'quần áo',
        ];

        foreach ($replacements as $short => $full) {
            // Thay thế từ lóng nếu nó đứng riêng lẻ
            $query = preg_replace("/\b$short\b/", $full, $query);
        }

        // 2. Loại bỏ từ thừa (Stopwords) để search tập trung vào TÊN SẢN PHẨM
        $stopWords = [
            'tôi',
            'muốn',
            'mua',
            'cần',
            'tìm',
            'có',
            'không',
            'bán',
            'giá',
            'rẻ',
            'bao',
            'nhiêu',
            'nhé',
            'ạ',
            'ơi',
            'shop',
            'ad',
            'mình',
            'cho',
            'hỏi',
            'với',
            'là',
            'những',
            'cái',
            'chiếc',
            'nào'
        ];

        $keywords = explode(' ', $query);

        // Lọc bỏ từ nhiễu
        $filteredKeywords = array_filter($keywords, function ($word) use ($stopWords) {
            return !in_array($word, $stopWords) && mb_strlen($word) > 1;
        });

        // Nếu lọc xong mà rỗng (vd khách chỉ chat "mua bán"), dùng lại query gốc
        if (empty($filteredKeywords)) {
            $filteredKeywords = $keywords;
        }

        // 3. Query Database thông minh hơn
        $products = Product::query()
            ->select('products.*', 'categories.name as category_name')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.status', 'active');

        $products->where(function ($q) use ($filteredKeywords) {
            foreach ($filteredKeywords as $word) {
                $q->orWhere('products.name', 'like', "%{$word}%")
                    ->orWhere('products.sku', 'like', "%{$word}%"); // Tìm theo cả mã SKU
            }
        });

        // Nếu search theo tên không ra, mới search mở rộng sang mô tả (để tránh nhiễu)
        $results = $products->take(8)->get();

        Log::info("Chatbot Search Keywords: " . implode(', ', $filteredKeywords));
        Log::info("Found: " . $results->count() . " products");

        return $results->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (int) $product->price,
                'price_display' => number_format((float) $product->price) . ' đ',
                'quantity' => $product->quantity,
                'image' => filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image),
                'slug' => $product->slug,
                'category' => $product->category_name ?? '',
            ];
        })->toArray();
    }
}
