<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Bắt đầu thiết lập tài khoản Admin...\n";

try {
    // 1. Đảm bảo Role 'admin' tồn tại
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    echo " - Role 'admin' đã sẵn sàng.\n";

    // 2. Tìm hoặc Tạo user admin@gmail.com
    $user = User::where('email', 'admin@gmail.com')->first();

    if (!$user) {
        $user = User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123123123'),
            'email_verified_at' => now(),
        ]);
        echo " - Đã tạo mới tài khoản admin@gmail.com (Mật khẩu: password).\n";
    } else {
        // Cập nhật lại mật khẩu cho chắc chắn
        $user->update(['password' => Hash::make('123123123')]);
        echo " - Đã tìm thấy tài khoản admin@gmail.com. Đã reset mật khẩu về: password.\n";
    }

    // 3. Gán Role admin cho User
    if (!$user->hasRole('admin')) {
        $user->assignRole($adminRole);
        echo " - Đã gán quyền 'admin' thành công.\n";
    } else {
        echo " - Tài khoản này đã có quyền 'admin' từ trước.\n";
    }

    echo "\nTHÀNH CÔNG! Bạn hãy đăng nhập bằng: admin@gmail.com / password\n";

} catch (\Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
    echo "Gợi ý: Hãy đảm bảo bạn đã chạy migration đầy đủ.\n";
}
