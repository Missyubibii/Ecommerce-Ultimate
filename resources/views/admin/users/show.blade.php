@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')
@section('header', 'Chi tiết: ' . $user->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Cột trái: Thông tin & Form Sửa --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- 1. Edit Main Info Form --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Thông tin tài khoản</h3>
                <span class="text-xs text-gray-500">ID: {{ $user->id }}</span>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="banned" {{ $user->status == 'banned' ? 'selected' : '' }}>Banned</option>
                        </select>
                    </div>
                </div>

                {{-- Roles Checkboxes --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò (Roles)</label>
                    <div class="flex gap-4">
                        {{-- Giả sử có Role 'admin' và 'customer', lấy từ Spatie --}}
                        @php
                            $allRoles = \Spatie\Permission\Models\Role::all();
                        @endphp
                        @foreach($allRoles as $role)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                    {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600 capitalize">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 text-right">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cập nhật thông tin
                    </button>
                </div>
            </form>
        </div>

        {{-- 2. Address List (Read-only View) --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Sổ địa chỉ ({{ $user->addresses->count() }})</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($user->addresses as $addr)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-600 truncate">{{ $addr->full_name }} <span class="text-gray-500">({{ $addr->phone }})</span></p>
                                <p class="mt-1 text-sm text-gray-500">{{ $addr->address_line1 }}, {{ $addr->city }}, {{ $addr->state }}</p>
                            </div>
                            @if($addr->is_default)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Default</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-sm text-gray-500 italic">User chưa có địa chỉ nào.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Cột phải: Avatar & Password Reset --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Avatar Card --}}
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <img class="h-32 w-32 rounded-full mx-auto object-cover border-4 border-gray-200"
                src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="">
            <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $user->name }}</h2>
            <p class="text-gray-500">{{ $user->email }}</p>
            <div class="mt-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($user->status) }}
                </span>
            </div>
        </div>

        {{-- Reset Password Form --}}
        <div class="bg-white rounded-lg shadow overflow-hidden border-t-4 border-yellow-400">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Đổi mật khẩu</h3>
                <p class="text-xs text-gray-500 mt-1">Admin có quyền set lại mật khẩu user.</p>
            </div>
            <form action="{{ route('admin.users.resetPassword', $user) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700">Mật khẩu mới</label>
                    <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none">
                    Đổi mật khẩu
                </button>
            </form>
        </div>

        {{-- Back Button --}}
        <div class="text-center">
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                ← Quay lại danh sách
            </a>
        </div>
    </div>
</div>
@endsection
