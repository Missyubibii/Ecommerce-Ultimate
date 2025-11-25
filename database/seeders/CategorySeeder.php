<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại để chèn ID tùy ý
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();

        // 1. DANH MỤC CHA (ID 1-20)
        $parents = [
            [1, 'Điện thoại - Máy tính bảng', 'dien-thoai-may-tinh-bang'],
            [2, 'Laptop - Máy tính', 'laptop-may-tinh'],
            [3, 'Âm thanh - Loa - Tai nghe', 'am-thanh-loa-tai-nghe'],
            [4, 'SmartHome - Nhà thông minh', 'smarthome-nha-thong-minh'],
            [5, 'Camera - An ninh', 'camera-an-ninh'],
            [6, 'Tivi - Màn hình', 'tivi-man-hinh'],
            [7, 'Linh kiện máy tính', 'linh-kien-may-tinh'],
            [8, 'Gaming Gear', 'gaming-gear'],
            [9, 'Phụ kiện điện thoại', 'phu-kien-dien-thoai'],
            [10, 'Thiết bị văn phòng', 'thiet-bi-van-phong'],
            [11, 'Router - Thiết bị mạng', 'router-thiet-bi-mang'],
            [12, 'Máy chơi game', 'may-choi-game'],
            [13, 'Thiết bị lưu trữ', 'thiet-bi-luu-tru'],
            [14, 'Máy ảnh - Quay phim', 'may-anh-quay-phim'],
            [15, 'Đồng hồ thông minh', 'dong-ho-thong-minh'],
            [16, 'Đồ gia dụng thông minh', 'do-gia-dung-thong-minh'],
            [17, 'Thiết bị đo - Công cụ số', 'thiet-bi-do-cong-cu-so'],
            [18, 'Phụ kiện máy tính', 'phu-kien-may-tinh'],
            [19, 'Pin - Sạc - Cáp', 'pin-sac-cap'],
            [20, 'Máy in - Scan', 'may-in-scan'],
        ];

        foreach ($parents as $cat) {
            DB::table('categories')->insert([
                'id' => $cat[0],
                'name' => $cat[1],
                'slug' => $cat[2],
                'parent_id' => null,
                'description' => $cat[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. DANH MỤC CON (ID 21-40)
        $children = [
            [21, 'Smartphone cao cấp', 'smartphone-cao-cap', 1],
            [22, 'Laptop gaming', 'laptop-gaming', 2],
            [23, 'Tai nghe Bluetooth', 'tai-nghe-bluetooth', 3],
            [24, 'Loa Bluetooth', 'loa-bluetooth', 3],
            [25, 'Thiết bị smarthome', 'thiet-bi-smarthome', 4],
            [26, 'Camera WiFi', 'camera-wifi', 5],
            [27, 'Smart TV', 'smart-tv', 6],
            [28, 'Màn hình máy tính', 'man-hinh-may-tinh', 6],
            [29, 'SSD - Ổ cứng', 'ssd-o-cung', 13],
            [30, 'Bàn phím cơ', 'ban-phim-co', 8],
            [31, 'Chuột gaming', 'chuot-gaming', 8],
            [32, 'Console game', 'console-game', 12],
            [33, 'Smartwatch phổ biến', 'smartwatch-pho-bien', 15],
            [34, 'Robot hút bụi', 'robot-hut-bui', 16],
            [35, 'Bếp điện thông minh', 'bep-dien-thong-minh', 16],
            [36, 'Router WiFi 6', 'router-wifi-6', 11],
            [37, 'Thẻ nhớ - USB', 'the-nho-usb', 13],
            [38, 'Ốp lưng - Kính cường lực', 'op-lung-kinh-cuong-luc', 9],
            [39, 'Pin dự phòng', 'pin-du-phong', 19],
            [40, 'Máy in laser', 'may-in-laser', 20],
        ];

        foreach ($children as $cat) {
            DB::table('categories')->insert([
                'id' => $cat[0],
                'name' => $cat[1],
                'slug' => $cat[2],
                'parent_id' => $cat[3],
                'description' => $cat[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
