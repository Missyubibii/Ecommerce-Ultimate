@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard Tổng quan')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-indigo-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tổng thành viên</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Đăng ký hôm nay</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['new_users_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500 opacity-60">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 p-3 rounded-full">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Đơn hàng</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Chào mừng đến với Admin Panel</h3>
        <p class="text-gray-600">Hệ thống đã được phân chia thành công. Sidebar bên trái dành cho các Modules quản trị. Sử dụng menu góc phải để quay về trang User.</p>
    </div>
@endsection
