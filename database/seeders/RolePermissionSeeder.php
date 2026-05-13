<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Tạo các Permissions (Quyền)
        $permissions = [
            'manage_products',
            'manage_categories',
            'manage_brands',
            'manage_banners',
            'manage_coupons',
            'manage_orders',
            'view_reports',
            'manage_chatbot',
            'manage_users',
            'manage_settings',
            'view_activity_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Tạo Roles và gán Permissions
        
        // ADMIN: Có tất cả các quyền
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // MANAGER (Nhân viên quản lý): Quyền giới hạn
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions([
            'manage_products',
            'manage_categories',
            'manage_brands',
            'manage_banners',
            'manage_coupons',
            'manage_orders',
            'view_reports',
            'manage_chatbot',
        ]);

        // USER (Khách hàng): Chỉ truy cập được trang mua sắm
        Role::firstOrCreate(['name' => 'user']);
    }
}
