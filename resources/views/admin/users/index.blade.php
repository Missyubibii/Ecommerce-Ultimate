@extends('layouts.admin')

@section('title', 'Quản lý Người dùng')
@section('header', 'Danh sách thành viên')

@php
    $initialData = [
        'search' => request('q', ''),
        'roleFilter' => request('role', ''),
        'statusFilter' => request('status', ''),
        'allIds' => $allUserIds, // ID từ Controller
    ];
@endphp

@section('content')
    <div x-data="userIndexPage(@js($initialData))" class="p-6 bg-white rounded-xl shadow-lg">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Người dùng</h1>
                <div class="mt-2 flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Tổng: {{ $users->total() }} tài khoản</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Search --}}
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" x-model="search" @keyup.enter="applyFilters()"
                            placeholder="Tìm tên, email, sđt..."
                            class="pl-10 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Filter: Role --}}
                <div class="min-w-[150px]">
                    <select x-model="roleFilter" @change="applyFilters()"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Vai trò --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter: Status --}}
                <div class="min-w-[150px]">
                    <select x-model="statusFilter" @change="applyFilters()"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Trạng thái --</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="banned">Banned</option>
                    </select>
                </div>

                <button @click="applyFilters()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">Lọc</button>
                <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 text-sm underline">Xóa
                    lọc</a>
            </div>
        </div>

        <div x-show="selectedIds.length > 0" x-transition style="display: none;"
            class="mb-4 p-3 bg-indigo-50 border border-indigo-100 rounded-lg flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-sm font-semibold text-indigo-800">Đã chọn <span x-text="selectedIds.length"></span> người
                    dùng</span>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="bulkAction('delete')"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm transition duration-300">
                    Xóa đã chọn
                </button>
                <button @click="selectedIds = []; selectAll = false;"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-1 px-3 rounded text-sm">
                    Hủy
                </button>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Thành viên</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Vai trò</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Ngày tham gia</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-4 py-3">
                                    <input type="checkbox" value="{{ $user->id }}" x-model="selectedIds"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-full object-cover mr-3"
                                            src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                                            alt="">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @foreach($user->roles as $role)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">
                                    @if($user->status === 'active')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @elseif($user->status === 'banned')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Banned</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                        class="text-indigo-600 hover:text-indigo-900 font-medium text-xs uppercase tracking-wider">Chi
                                        tiết</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Không tìm thấy kết quả.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userIndexPage', (initialData) => ({
                search: initialData.search,
                roleFilter: initialData.roleFilter,
                statusFilter: initialData.statusFilter,
                allIds: initialData.allIds,
                selectedIds: [],
                selectAll: false,

                applyFilters() {
                    let params = new URLSearchParams(window.location.search);

                    if (this.search) params.set('q', this.search); else params.delete('q');
                    if (this.roleFilter) params.set('role', this.roleFilter); else params.delete('role');
                    if (this.statusFilter) params.set('status', this.statusFilter); else params.delete('status');

                    params.delete('page');
                    window.location.search = params.toString();
                },

                toggleSelectAll() {
                    this.selectedIds = this.selectAll ? this.allIds : [];
                },

                bulkAction(action) {
                    if (this.selectedIds.length === 0) return;

                    if (confirm('Bạn có chắc chắn muốn xóa ' + this.selectedIds.length + ' người dùng đã chọn? Hành động này không thể hoàn tác.')) {

                        // Gọi AJAX lên Server
                        fetch('{{ route('admin.users.bulkDelete') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                ids: this.selectedIds
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert(data.message);
                                    // Reload lại trang để cập nhật danh sách
                                    window.location.reload();
                                } else {
                                    alert('Có lỗi xảy ra: ' + (data.message || 'Unknown error'));
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Đã xảy ra lỗi hệ thống.');
                            });
                    }
                }
            }));
        });
    </script>
@endsection
