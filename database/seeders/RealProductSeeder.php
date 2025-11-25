<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RealProductSeeder extends Seeder
{
    public function run(): void
    {
        $downloadQueue = [];

        // CATALOG MẪU (Đã cập nhật Link ảnh sống)
        $catalog = [
            // 1. iPhone 15 Pro Max
            [
                'category_id' => 21,
                'name' => 'iPhone 15 Pro Max 256GB Titan Tự Nhiên',
                'price' => 29990000,
                'images' => [
                    'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-15-pro-max_3.jpg',
                    'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-15-pro-max_4.jpg'
                ]
            ],
            // 2. Samsung S24 Ultra
            [
                'category_id' => 21,
                'name' => 'Samsung Galaxy S24 Ultra 12GB 256GB',
                'price' => 26990000,
                'images' => [
                    'https://cdn.tgdd.vn/Products/Images/42/307174/samsung-galaxy-s24-ultra-grey-1.jpg',
                    'https://cdn.tgdd.vn/Products/Images/42/307174/samsung-galaxy-s24-ultra-grey-2.jpg'
                ]
            ],
            // 3. ASUS ROG Strix
            [
                'category_id' => 22,
                'name' => 'Laptop ASUS ROG Strix G16 G614JU',
                'price' => 32990000,
                'images' => [
                    'https://cdn.tgdd.vn/Products/Images/44/303538/asus-rog-strix-g16-g614ju-i7-n3777w-1.jpg',
                    'https://cdn.tgdd.vn/Products/Images/44/303538/asus-rog-strix-g16-g614ju-i7-n3777w-2.jpg'
                ]
            ],
            // 4. Sony Headphone
            [
                'category_id' => 23,
                'name' => 'Tai nghe Bluetooth Sony WF-1000XM5',
                'price' => 5490000,
                'images' => [
                    'https://cdn.tgdd.vn/Products/Images/54/309963/sony-wf-1000xm5-den-1.jpg',
                    'https://cdn.tgdd.vn/Products/Images/54/309963/sony-wf-1000xm5-den-2.jpg'
                ]
            ],
            // 5. Loa Marshall
            [
                'category_id' => 24,
                'name' => 'Loa Bluetooth Marshall Emberton II',
                'price' => 3990000,
                'images' => [
                    'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/l/o/loa-bluetooth-marshall-emberton-ii-cream-1.jpg'
                ]
            ],
            // 6. Camera Imou
            [
                'category_id' => 26,
                'name' => 'Camera IP Wifi Imou Ranger 2C',
                'price' => 590000,
                'images' => [
                    'https://cdn.tgdd.vn/Products/Images/4728/237845/camera-ip-360-do-1080p-imou-ranger-2-a22ep-1-1.jpg'
                ]
            ],
             // 7. Smart TV Samsung
            [
                'category_id' => 27,
                'name' => 'Smart Tivi Samsung 4K 55 inch',
                'price' => 8990000,
                'images' => [
                    'https://cdn.tgdd.vn/Products/Images/1942/289475/smart-tivi-samsung-4k-55-inch-ua55au7002-1.jpg'
                ]
            ],
             // 8. SSD Samsung
            [
                'category_id' => 29,
                'name' => 'Ổ cứng SSD Samsung 980 PRO 1TB',
                'price' => 2450000,
                'images' => [
                    'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/s/ssd-samsung-980-pro-pcie-4-0-nvme-m-2-1tb-mz-v8p1t0bw.jpg'
                ]
            ],
             // 9. Keyboard Logitech
            [
                'category_id' => 30,
                'name' => 'Bàn phím cơ Logitech G Pro X TKL',
                'price' => 3290000,
                'images' => [
                    'https://cdn.tgdd.vn/Products/Images/4547/235658/ban-phim-co-gaming-logitech-g-pro-x-1-org.jpg'
                ]
            ],
             // 10. Mouse Logitech
            [
                'category_id' => 31,
                'name' => 'Chuột Gaming Logitech G502 HERO',
                'price' => 990000,
                'images' => [
                    'https://cdn.tgdd.vn/Products/Images/86/199763/chuot-gaming-logitech-g502-hero-den-1-1.jpg'
                ]
            ],
             // 11. PS5
            [
                'category_id' => 32,
                'name' => 'Máy chơi game Sony PlayStation 5 Slim',
                'price' => 11990000,
                'images' => [
                    'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/p/l/playstation_5_slim.png'
                ]
            ],
             // 12. Apple Watch
            [
                'category_id' => 33,
                'name' => 'Apple Watch Series 9 GPS 41mm',
                'price' => 9490000,
                'images' => [
                    'https://cdn.tgdd.vn/Products/Images/7077/314734/apple-watch-s9-gps-41mm-vien-nhom-day-silicone-hong-1.jpg'
                ]
            ],
        ];

        // Tạo ngẫu nhiên 50 sản phẩm từ catalog mẫu (Không tạo biến thể màu sắc để tránh trùng ảnh)
        // Thay vào đó, ta sẽ tạo sản phẩm khác nhau dựa trên catalog gốc
        for ($i = 0; $i < 50; $i++) {
            $template = $catalog[array_rand($catalog)]; // Lấy ngẫu nhiên 1 mẫu

            // Tạo tên hơi khác đi một chút để phân biệt
            $suffixes = ['(Bản Quốc Tế)', '(Chính Hãng VN/A)', '(Mới 99%)', '(Fullbox)', '(Nhập Khẩu)'];
            $name = $template['name'] . ' ' . $suffixes[array_rand($suffixes)];

            // Giá random +/- 10%
            $price = $template['price'] * (1 + rand(-10, 10) / 100);
            $price = round($price, -4); // Làm tròn

            $this->createProductAndQueueImage([
                'category_id' => $template['category_id'],
                'name' => $name,
                'price' => $price,
                'images' => $template['images'] // Dùng lại ảnh gốc (chắc chắn tải được)
            ], $downloadQueue);
        }

        // Lưu file JSON
        $filePath = storage_path('app/product_images.json');
        file_put_contents($filePath, json_encode($downloadQueue, JSON_PRETTY_PRINT));

        $this->command->info('Đã tạo 50 sản phẩm mẫu và ghi file JSON: ' . $filePath);
    }

    private function createProductAndQueueImage($data, &$queue)
    {
        $slug = Str::slug($data['name']) . '-' . Str::random(4);
        $sku = 'SKU-' . Str::upper(Str::random(6));

        Product::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $slug,
            'sku' => $sku,
            'price' => $data['price'],
            'cost_price' => $data['price'] * 0.8,
            'quantity' => rand(10, 50),
            'status' => 'active',
            'description' => 'Sản phẩm chính hãng chất lượng cao.',
            'image' => 'products/' . $slug . '-1.jpg', // Placeholder path
        ]);

        if (!empty($data['images'])) {
            $queue[] = [
                'slug' => $slug,
                'images' => $data['images']
            ];
        }
    }
}
