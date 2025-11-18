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

        // 2. Tạo Roles (Vai trò) [cite: 193, 421]
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleCustomer = Role::firstOrCreate(['name' => 'customer']);

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
        $admin->assignRole($roleAdmin);

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
        $customer->assignRole($roleCustomer);

        // Optional: Tạo thêm vài user ngẫu nhiên để test phân trang
        // User::factory(10)->create()->each(function ($u) use ($roleCustomer) {
        //     $u->assignRole($roleCustomer);
        // });
    }
}
