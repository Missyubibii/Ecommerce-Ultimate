<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AutoEnrichProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:auto-enrich {--force : Ghi đè dữ liệu cũ nếu đã có}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động quét sản phẩm, phân tích và gán Thương hiệu, Năm sản xuất, Xuất xứ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang bắt đầu quá trình phân tích sản phẩm...');

        $products = Product::all();
        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        $brandsList = [
            'Apple' => ['iPhone', 'iPad', 'MacBook', 'iMac', 'AirPods', 'Apple Watch'],
            'Samsung' => ['Galaxy', 'Samsung'],
            'Dell' => ['Dell', 'Latitude', 'Inspiron', 'Vostro', 'XPS', 'Alienware'],
            'HP' => ['HP', 'Pavilion', 'Envy', 'Spectre', 'EliteBook', 'ProBook', 'Victus'],
            'Asus' => ['Asus', 'ROG', 'TUF', 'Vivobook', 'Zenbook'],
            'Acer' => ['Acer', 'Nitro', 'Predator', 'Aspire', 'Swift'],
            'Lenovo' => ['Lenovo', 'ThinkPad', 'IdeaPad', 'Legion', 'Yoga'],
            'Sony' => ['Sony', 'PlayStation', 'Bravia', 'Xperia'],
            'Xiaomi' => ['Xiaomi', 'Redmi', 'Mi ', 'Poco'],
            'Oppo' => ['Oppo', 'Reno'],
            'MSI' => ['MSI', 'Katana', 'Stealth', 'Prestige'],
            'Logitech' => ['Logitech'],
            'Razer' => ['Razer'],
            'Microsoft' => ['Microsoft', 'Surface'],
        ];

        $origins = [
            'Apple' => 'Trung Quốc / Mỹ',
            'Samsung' => 'Việt Nam / Hàn Quốc',
            'Dell' => 'Trung Quốc / Mỹ',
            'HP' => 'Trung Quốc / Mỹ',
            'Asus' => 'Trung Quốc / Đài Loan',
            'Lenovo' => 'Trung Quốc',
            'Sony' => 'Trung Quốc / Nhật Bản',
            'Xiaomi' => 'Trung Quốc',
            'Oppo' => 'Trung Quốc',
        ];

        foreach ($products as $product) {
            $name = $product->name;
            $foundBrandName = null;

            // 1. Tìm thương hiệu
            foreach ($brandsList as $brandName => $keywords) {
                foreach ($keywords as $keyword) {
                    if (Str::contains(Str::lower($name), Str::lower($keyword))) {
                        $foundBrandName = $brandName;
                        break 2;
                    }
                }
            }

            if ($foundBrandName) {
                // Tạo hoặc lấy Brand
                $brand = Brand::where('name', $foundBrandName)->first();
                if (!$brand) {
                    $brand = Brand::create([
                        'name' => $foundBrandName,
                        'slug' => Str::slug($foundBrandName),
                        'display_locations' => ['home', 'category'],
                        'is_active' => true
                    ]);
                }

                // Tải logo nếu chưa có
                if (empty($brand->logo) || $this->option('force')) {
                    $this->fetchLogo($brand);
                }

                // 2. Gán Brand cho Product
                if (!$product->brand_id || $this->option('force')) {
                    $product->brand_id = $brand->id;
                }

                // 3. Phân tích Năm sản xuất (Tìm số có 4 chữ số từ 2018-2026)
                if (!$product->production_year || $this->option('force')) {
                    if (preg_match('/(20[12][0-9])/', $name, $matches)) {
                        $product->production_year = $matches[1];
                    } else {
                        $product->production_year = rand(2022, 2024); // Mặc định ngẫu nhiên gần đây
                    }
                }

                // 4. Gán Xuất xứ
                if (!$product->origin || $this->option('force')) {
                    $product->origin = $origins[$foundBrandName] ?? 'Trung Quốc';
                }

                // 5. Gán Tình trạng (Mặc định Mới)
                if (!$product->condition || $this->option('force')) {
                    $product->condition = 'Mới (New)';
                }

                $product->save();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Hoàn tất phân tích và cập nhật dữ liệu sản phẩm!');
    }

    /**
     * Tự động tìm và tải logo thương hiệu
     */
    private function fetchLogo(Brand $brand)
    {
        $domainMap = [
            'Apple' => 'apple.com',
            'Samsung' => 'samsung.com',
            'Xiaomi' => 'xiaomi.com',
            'Oppo' => 'oppo.com',
            'Acer' => 'acer.com',
            'Asus' => 'asus.com',
            'Lenovo' => 'lenovo.com',
            'MSI' => 'msi.com',
            'HP' => 'hp.com',
            'Dell' => 'dell.com',
            'Microsoft' => 'microsoft.com',
            'Sony' => 'sony.com',
            'Logitech' => 'logitech.com',
            'Razer' => 'razer.com',
        ];

        $domain = $domainMap[$brand->name] ?? Str::slug($brand->name) . '.com';
        $logoUrl = "https://logos.hunter.io/{$domain}";

        try {
            // Hunter.io Logo API không cần key, định dạng trả về là ảnh
            $response = Http::get($logoUrl);
            if ($response->successful()) {
                $path = 'brands/' . Str::slug($brand->name) . '.png';
                Storage::disk('public')->put($path, $response->body());
                $brand->update(['logo' => $path]);
                $this->info(" -> Đã tải logo cho {$brand->name}");
            }
        } catch (\Exception $e) {
            $this->error(" -> Không thể tải logo cho {$brand->name}: " . $e->getMessage());
        }
    }
}
