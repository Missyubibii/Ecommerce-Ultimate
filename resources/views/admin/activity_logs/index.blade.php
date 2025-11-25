@extends('layouts.admin')

@section('title', 'Nhật ký Hoạt động')
@section('header', 'Activity Logs')

@php
    $initialData = [
        'search' => request('subject_type', ''),
        'eventFilter' => request('event', ''),
    ];
@endphp

@section('content')
    <div x-data="logIndexPage(@js($initialData))" class="p-6 bg-white rounded-xl shadow-lg">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nhật ký Hoạt động</h1>
                <div class="mt-2 flex items-center">
                    <div class="w-3 h-3 bg-gray-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Tổng logs: {{ $activities->total() }}</span>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Search Subject --}}
                <div class="flex-1 min-w-[200px]">
                    <input type="text" x-model="search" @keyup.enter="applyFilters()"
                        placeholder="Tìm đối tượng (VD: App\Models\Product)..."
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 pl-3">
                </div>

                {{-- Event Filter --}}
                <div class="min-w-[150px]">
                    <select x-model="eventFilter" @change="applyFilters()"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Hành động --</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                        <option value="login">Login</option>
                    </select>
                </div>

                <button @click="applyFilters()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                    Lọc
                </button>
                <a href="{{ route('admin.activity_logs.index') }}"
                    class="text-gray-500 hover:text-gray-700 text-sm underline">Reset</a>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Thời gian</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Mô tả</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Đối tượng</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Người thực hiện</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($activities as $log)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-bold
                                                        {{ $log->event == 'created' ? 'bg-green-100 text-green-800' :
                            ($log->event == 'updated' ? 'bg-yellow-100 text-yellow-800' :
                                ($log->event == 'deleted' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ ucfirst($log->event) }}
                                                </span>
                                                <span class="ml-2 text-gray-700">{{ $log->description }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-xs font-mono text-gray-600">
                                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $log->causer ? $log->causer->name : 'System/Guest' }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                {{-- Nút xem chi tiết thay đổi (JSON) - Có thể làm Modal sau --}}
                                                <button class="text-indigo-600 hover:text-indigo-900 text-xs"
                                                    onclick="alert('Properties: {{ json_encode($log->properties) }}')">
                                                    Xem Data
                                                </button>
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    Chưa có log nào được ghi lại.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $activities->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('logIndexPage', (init) => ({
                search: init.search,
                eventFilter: init.eventFilter,
                applyFilters() {
                    let params = new URLSearchParams(window.location.search);
                    if (this.search) params.set('subject_type', this.search); else params.delete('subject_type');
                    if (this.eventFilter) params.set('event', this.eventFilter); else params.delete('event');
                    window.location.search = params.toString();
                }
            }));
        });
    </script>
@endsection
