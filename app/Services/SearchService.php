<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SearchTerm;
use App\Jobs\LogSearchHistory;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    /**
     * Xử lý tìm kiếm chính (Main Search Engine)
     */
    public function search(string $keyword, array $filters = []): LengthAwarePaginator
    {
        // Chuẩn hóa từ khóa: bỏ khoảng trắng thừa
        $keyword = trim($keyword);

        $query = Product::query()
            ->with(['category', 'product_images'])
            ->where('status', 'active');

        // --- LOGIC TÌM KIẾM NÂNG CAO ---
        if (!empty($keyword)) {
            $query->where(function (Builder $q) use ($keyword) {
                // 1. Tìm theo Tên sản phẩm
                $q->where('name', 'like', "%{$keyword}%")

                    // 2. Tìm theo SKU (Mã sản phẩm)
                    ->orWhere('sku', 'like', "%{$keyword}%")

                    // 3. Tìm không dấu (Hack: Tìm vào slug)
                    ->orWhere('slug', 'like', '%' . \Illuminate\Support\Str::slug($keyword) . '%')
                    ->orWhere('slug', 'like', "%{$keyword}%") // Fallback

                    // 4. Tìm theo Tên Danh Mục (Category Name)
                    ->orWhereHas('category', function (Builder $catQuery) use ($keyword) {
                        $catQuery->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        // --- CÁC BỘ LỌC KHÁC (FILTERS) ---

        // Lọc theo Danh mục (từ sidebar)
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Lọc theo Giá
        if (!empty($filters['price_from'])) {
            $query->where('price', '>=', $filters['price_from']);
        }
        if (!empty($filters['price_to'])) {
            $query->where('price', '<=', $filters['price_to']);
        }

        // Sắp xếp (Sorting)
        $sort = $filters['sort'] ?? 'created_at';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default: // created_at
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Thực thi phân trang
        $results = $query->paginate($filters['per_page'] ?? 12);

        // Dispatch Job ghi log (chỉ ghi trang 1 để tránh spam log khi user next page)
        if (empty($filters['page']) || $filters['page'] == 1) {
            $this->logSearch($keyword, $results->total());
        }

        return $results;
    }

    public function getSuggestions(string $partial): \Illuminate\Support\Collection
    {
        if (empty(trim($partial))) return collect([]);

        return Product::query()
            ->where('status', 'active')
            ->where(function ($q) use ($partial) {
                $q->where('name', 'like', "%{$partial}%")
                    ->orWhere('slug', 'like', '%' . \Illuminate\Support\Str::slug($partial) . '%');
            })
            ->limit(5)
            ->pluck('name');
    }

    public function getTrendingKeywords(int $limit = 5)
    {
        return cache()->remember('search_trending', 3600, function () use ($limit) {
            return SearchTerm::orderByDesc('hits')
                ->limit($limit)
                ->get();
        });
    }

    protected function logSearch(string $keyword, int $count): void
    {
        if (empty(trim($keyword))) return;

        $logData = [
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'keyword' => strtolower(trim($keyword)),
            'results_count' => $count,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ];

        LogSearchHistory::dispatch($logData);
    }
}
