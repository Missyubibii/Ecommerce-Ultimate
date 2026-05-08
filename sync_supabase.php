<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Vì đây là script chạy ngoài Laravel, ta cần load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sqlFile = __DIR__ . '/ecommerce.sql';
if (!file_exists($sqlFile)) {
    die("Không tìm thấy file ecommerce.sql\n");
}

// Danh sách các bảng cần đồng bộ
$targetTables = [
    'categories',
    'products',
    'product_images',
    'product_colors',
    'product_variants',
    'banners',
    'roles',
    'permissions',
    'model_has_roles',
    'role_has_permissions'
];

echo "Bắt đầu dọn dẹp dữ liệu cũ (Giữ lại users)...\n";

Schema::disableForeignKeyConstraints();

foreach (array_reverse($targetTables) as $table) {
    if (Schema::hasTable($table)) {
        echo " - Đang xóa dữ liệu bảng: $table\n";
        DB::table($table)->truncate();
    }
}

echo "Bắt đầu đọc file SQL và đồng bộ (500MB)...\n";

$handle = fopen($sqlFile, 'r');
$currentTable = '';
$count = 0;

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '--') || str_starts_with($line, '/*')) {
            continue;
        }

        // Kiểm tra xem dòng này có phải là INSERT INTO của bảng ta cần không
        $match = false;
        foreach ($targetTables as $table) {
            // Regex linh hoạt: hỗ trợ "INSERT INTO products" hoặc "INSERT INTO `products`"
            if (preg_match("/INSERT INTO\s+`?" . $table . "`?/", $line)) {
                $currentTable = $table;
                $match = true;
                break;
            }
        }

        if ($match || (str_starts_with($line, '(') && $currentTable !== '')) {
            // Chuyển đổi cú pháp MySQL sang Postgres
            // 1. Thay dấu nháy ngược ` bằng rỗng hoặc "
            $convertedLine = str_replace('`', '"', $line);
            
            // 2. Xử lý các giá trị đặc biệt nếu cần (ví dụ: \')
            $convertedLine = str_replace("\\'", "''", $convertedLine);
            
            // 3. Kết thúc câu lệnh nếu là dòng INSERT mới
            if (str_starts_with($convertedLine, 'INSERT INTO')) {
                // Đảm bảo kết thúc bằng dấu chấm phẩy nếu dòng tiếp theo không phải là dữ liệu
                // Nhưng trong dump MySQL, thường 1 câu INSERT có nhiều values
            }

            try {
                // Nếu dòng kết thúc bằng dấu phẩy, ta cần đổi thành chấm phẩy để chạy đơn lẻ (nếu script đọc từng dòng)
                // Tuy nhiên, MySQL dump thường gộp nhiều dòng. Cách tốt nhất là gộp lại cho đến khi gặp dấu ;
                
                static $buffer = '';
                $buffer .= $convertedLine . " ";
                
                if (str_ends_with(trim($convertedLine), ';')) {
                    DB::statement($buffer);
                    $buffer = '';
                    $count++;
                    if ($count % 50 == 0) {
                        echo " - Đã xử lý $count câu lệnh INSERT...\n";
                    }
                }
            } catch (\Exception $e) {
                echo "Lỗi tại bảng $currentTable: " . $e->getMessage() . "\n";
                $buffer = ''; // Reset buffer khi lỗi
            }
        } else {
            $currentTable = ''; // Reset nếu không phải bảng đích
        }
    }
    fclose($handle);
}

Schema::enableForeignKeyConstraints();

echo "Hoàn thành! Đã đồng bộ thành công dữ liệu từ Laragon sang Supabase.\n";
