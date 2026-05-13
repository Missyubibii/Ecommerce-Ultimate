<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Reset cache của Spatie Permission để tránh lỗi
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Tạo Roles & Permissions qua Seeder
        $this->call(RolePermissionSeeder::class);

        // 3. Tạo tài khoản ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123123123'),
                'phone' => '0909000111',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // 4. Tạo tài khoản CUSTOMER (User thường)
        $customer = User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Khách Hàng Demo',
                'password' => Hash::make('123123123'),
                'phone' => '0909000222',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $customer->assignRole('user');

        // Optional: Tạo thêm vài user ngẫu nhiên để test phân trang
        // User::factory(10)->create()->each(function ($u) use ($roleCustomer) {
        //     $u->assignRole($roleCustomer);
        // });

        $this->call(CategorySeeder::class);

        // 3. Chạy Product Seeder (Tạo sản phẩm gắn vào ID 21-40)
        $this->call(RealProductSeeder::class);
    }
}
