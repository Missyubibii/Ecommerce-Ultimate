<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductImage;

class DownloadProductImages extends Command
{
    protected $signature = 'products:download-images';
    protected $description = 'Tải ảnh sản phẩm từ file JSON và lưu vào local storage, có fallback nếu link gốc lỗi.';

    public function handle()
    {
        $filePath = storage_path('app/product_images.json');

        if (!file_exists($filePath)) {
            $this->error("File không tồn tại: $filePath");
            return 1;
        }

        $json = json_decode(file_get_contents($filePath), true);
        $this->info("Bắt đầu tải ảnh cho " . count($json) . " sản phẩm...");

        $bar = $this->output->createProgressBar(count($json));
        $bar->start();

        foreach ($json as $item) {
            $slug = $item['slug'];
            $product = Product::where('slug', $slug)->first();

            if (!$product) {
                $this->warn("\nKhông tìm thấy sản phẩm slug: $slug");
                continue;
            }

            // Xóa ảnh gallery cũ để không bị trùng sau khi tải lại
            $product->images()->delete();

            foreach ($item['images'] as $index => $imgUrl) {
                try {
                    $fileName = $slug . '-' . ($index + 1);
                    $saveDir = 'products/';
                    $responseBody = null;
                    $successfulDownload = false;
                    $finalExtension = 'jpg';

                    // --- THỬ LINK GỐC ---
                    $response = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                    ])->timeout(15)->get($imgUrl);

                    if ($response->successful()) {
                        $responseBody = $response->body();
                        $finalExtension = Str::after(pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION), '?') ?: 'jpg';
                        $this->line(" -> Tải thành công link gốc.");
                        $successfulDownload = true;
                    } else {
                        // --- FALLBACK VĨNH VIỄN (PICSUM) ---
                        $this->warn("\n[404 Link chết/Fallback] Đang tải ảnh thay thế ổn định (Picsum)...");

                        // Sử dụng ID sản phẩm làm seed cho ảnh (đảm bảo mỗi sản phẩm có ảnh khác nhau)
                        $seed = $product->id + $index;
                        $fallbackUrl = "https://picsum.photos/seed/$seed/800/600";

                        $fallbackResponse = Http::timeout(15)->get($fallbackUrl);

                        if ($fallbackResponse->successful()) {
                            $responseBody = $fallbackResponse->body();
                            $finalExtension = 'jpeg';
                            $this->warn(" -> Đã lưu ảnh thay thế (Picsum).");
                            $successfulDownload = true;
                        } else {
                            // Nếu Picsum vẫn lỗi (rất hiếm)
                            $this->error(" -> THẤT BẠI HOÀN TOÀN.");
                        }
                    }

                    // 3. Lưu file và cập nhật DB
                    if ($successfulDownload && $responseBody) {
                        $savePath = $saveDir . $fileName . '.' . $finalExtension;
                        Storage::disk('public')->put($savePath, $responseBody);

                        if ($index === 0) {
                            $product->update(['image' => $savePath]);
                        } else {
                            // Tạo ảnh Gallery
                            $product->images()->create([
                                'product_id' => $product->id,
                                'path' => $savePath,
                                'sort_order' => $index
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    $this->error("\n[EXCEPTION] Lỗi xử lý $slug: " . $e->getMessage());
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n\nHoàn tất tải ảnh! Đừng quên chạy lệnh: php artisan storage:link");
        return 0;
    }
}
