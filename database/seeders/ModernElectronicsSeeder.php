<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ModernElectronicsSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('categories')->truncate();

        $structure = [
            'Điện thoại' => [
                'iPhone (Apple)', 'Samsung Galaxy', 'Xiaomi & Redmi', 'OPPO', 'Vivo', 'Realme', 'Nokia', 'Điện thoại phổ thông'
            ],
            'Laptop' => [
                'MacBook (Apple)', 'Laptop Gaming', 'Laptop Văn phòng', 'Laptop Mỏng nhẹ', 'Laptop Đồ họa', 'Laptop Cảm ứng - 2 in 1'
            ],
            'Linh kiện PC' => [
                'CPU - Bộ vi xử lý', 'VGA - Card màn hình', 'Mainboard - Bo mạch chủ', 'RAM - Bộ nhớ trong', 'PSU - Nguồn máy tính', 'Case - Vỏ máy tính', 'Tản nhiệt (Khí/Nước)', 'Ổ cứng SSD'
            ],
            'Máy tính để bàn' => [
                'PC Gaming', 'PC Văn phòng', 'PC Đồ họa - Render', 'Workstation', 'Mini PC', 'PC All-in-One'
            ],
            'Màn hình máy tính' => [
                'Màn hình Gaming', 'Màn hình Đồ họa', 'Màn hình 4K', 'Màn hình Cong', 'Màn hình Văn phòng', 'Màn hình Di động'
            ],
            'Âm thanh' => [
                'Tai nghe Bluetooth', 'Tai nghe Gaming', 'Loa Bluetooth', 'Loa Soundbar', 'Loa Vi tính', 'Dàn âm thanh Karaoke', 'Micro - Thiết bị thu âm'
            ],
            'Đồng hồ thông minh' => [
                'Apple Watch', 'Samsung Galaxy Watch', 'Garmin', 'Amazfit', 'Huawei Watch', 'Vòng đeo tay thông minh (Band)'
            ],
            'Thiết bị lưu trữ' => [
                'Ổ cứng SSD di động', 'Ổ cứng HDD di động', 'USB Flash', 'Thẻ nhớ (SD/MicroSD)', 'Box đựng ổ cứng'
            ],
            'Thiết bị mạng' => [
                'Router WiFi 6', 'Bộ kích sóng WiFi', 'Switch - Bộ chia mạng', 'USB WiFi', 'Thiết bị 4G/5G di động'
            ],
            'Game & Console' => [
                'PlayStation 5 (PS5)', 'Nintendo Switch', 'Xbox Series', 'Tay cầm chơi game', 'Máy chơi game cầm tay (Steam Deck/ROG Ally)'
            ],
            'Máy ảnh & Quay phim' => [
                'Máy ảnh Mirrorless', 'Máy ảnh DSLR', 'Action Camera (GoPro)', 'Máy ảnh Du lịch', 'Ống kính (Lens)', 'Gimbal - Chống rung'
            ],
            'Đồ gia dụng thông minh' => [
                'Robot hút bụi', 'Máy lọc không khí', 'Khóa cửa vân tay', 'Đèn thông minh', 'Camera giám sát (IP WiFi)', 'Máy hút bụi cầm tay'
            ],
            'Thiết bị văn phòng' => [
                'Máy in Laser', 'Máy in Phun màu', 'Máy chiếu', 'Máy quét (Scan)', 'Máy chấm công', 'Máy hủy tài liệu'
            ],
            'Phụ kiện công nghệ' => [
                'Sạc dự phòng', 'Cáp sạc & Củ sạc', 'Hub - Cổng chuyển đổi', 'Bàn phím không dây', 'Chuột máy tính', 'Giá đỡ điện thoại/Laptop'
            ],
            'Tivi & Giải trí' => [
                'Smart TV 4K', 'Tivi OLED/QLED', 'Android TV Box', 'Máy chiếu Mini', 'Loa thanh (Soundbar)'
            ]
        ];

        echo "Bắt đầu tạo 15 danh mục cha và 100+ danh mục con...\n";

        foreach ($structure as $parentName => $children) {
            $parentId = DB::table('categories')->insertGetId([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'parent_id' => null,
                'description' => "Chuyên mục $parentName với các thương hiệu hàng đầu.",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo " + Đang tạo: $parentName\n";

            foreach ($children as $childName) {
                DB::table('categories')->insert([
                    'name' => $childName,
                    'slug' => Str::slug($childName) . '-' . rand(100, 999),
                    'parent_id' => $parentId,
                    'description' => "Các sản phẩm $childName chính hãng giá tốt.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::enableForeignKeyConstraints();
        echo "\nTHÀNH CÔNG! Đã tạo xong hệ thống danh mục đồ sộ cho website.\n";
    }
}
