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

            <div x-data="{ 
                showModal: false, 
                originalRole: '{{ $user->roles->first()->name ?? '' }}',
                selectedRole: '{{ $user->roles->first()->name ?? '' }}',
                submitForm() {
                    if (this.selectedRole !== this.originalRole) {
                        this.showModal = true;
                    } else {
                        this.$refs.userForm.submit();
                    }
                }
            }">
                <form x-ref="userForm" action="{{ route('admin.users.update', $user) }}" method="POST" @submit.prevent="submitForm()" class="p-6 space-y-4">
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
                                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Vô hiệu hóa</option>
                                <option value="banned" {{ $user->status == 'banned' ? 'selected' : '' }}>Cấm tài khoản</option>
                            </select>
                        </div>
                    </div>

                    {{-- Roles Radio Buttons --}}
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Vai trò (Role - Chọn duy nhất 1)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @php
                                $allRoles = \Spatie\Permission\Models\Role::all();
                            @endphp
                            @foreach($allRoles as $role)
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none transition-all"
                                    :class="selectedRole === '{{ $role->name }}' ? 'border-indigo-500 ring-2 ring-indigo-500' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="roles[]" value="{{ $role->name }}" x-model="selectedRole"
                                        class="sr-only">
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-bold" :class="selectedRole === '{{ $role->name }}' ? 'text-indigo-900' : 'text-gray-900'">
                                                @if($role->name === 'admin') Quản trị viên
                                                @elseif($role->name === 'manager') Quản lý
                                                @else Người dùng
                                                @endif
                                            </span>
                                            <span class="mt-1 flex items-center text-xs text-gray-500">
                                                {{ $role->name === 'admin' ? 'Toàn quyền hệ thống' : ($role->name === 'manager' ? 'Quản lý nghiệp vụ' : 'Mua sắm & Profile') }}
                                            </span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-indigo-600" :class="selectedRole === '{{ $role->name }}' ? '' : 'hidden'" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 text-right">
                        <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-bold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition transform hover:scale-105">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>

                {{-- Confirmation Modal --}}
                <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900">Xác nhận thay đổi quyền hạn</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Bạn đang thay đổi vai trò của người dùng này từ <span class="font-bold text-red-600" x-text="originalRole"></span> sang <span class="font-bold text-green-600" x-text="selectedRole"></span>. 
                                            Việc này sẽ thay đổi ngay lập tức các quyền truy cập của họ. Bạn có chắc chắn?
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button @click="$refs.userForm.submit()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                    Đồng ý thay đổi
                                </button>
                                <button @click="showModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                                    Hủy bỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Mặc định</span>
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
                src="{{ $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/'.$user->avatar)) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="">
            <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $user->name }}</h2>
            <p class="text-gray-500">{{ $user->email }}</p>
            <div class="mt-4">
                @foreach ($user->roles as $role)
                    @php
                        $roleClass = 'bg-gray-100 text-gray-800';
                        $roleName = 'Người dùng';
                        if ($role->name === 'admin') {
                            $roleClass = 'bg-blue-100 text-blue-800';
                            $roleName = 'Quản trị viên';
                        } elseif ($role->name === 'manager') {
                            $roleClass = 'bg-purple-100 text-purple-800';
                            $roleName = 'Quản lý';
                        }
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleClass }}">
                        {{ $roleName }}
                    </span>
                @endforeach
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
