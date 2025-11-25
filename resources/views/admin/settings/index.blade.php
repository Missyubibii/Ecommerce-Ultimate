@extends('layouts.admin')

@section('title', 'Cấu hình Hệ thống')
@section('header', 'Thiết lập chung')

@section('content')
    <div class="p-6 bg-white rounded-xl shadow-lg" x-data="{ activeTab: 'general' }">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Cấu hình Hệ thống</h1>
                <p class="text-sm text-gray-500 mt-1">Quản lý các tham số toàn cục của website.</p>
            </div>
            <button form="settingsForm" type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                    </path>
                </svg>
                Lưu cấu hình
            </button>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div
                class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabs --}}
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                @foreach($settingsGrouped as $group => $items)
                    <button @click="activeTab = '{{ $group }}'"
                        :class="activeTab === '{{ $group }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm capitalize">
                        {{ $group }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Form Content --}}
        <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @foreach($settingsGrouped as $group => $settings)
                <div x-show="activeTab === '{{ $group }}'" class="space-y-6">
                    @foreach($settings as $setting)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center border-b border-gray-50 pb-6 last:border-0">
                            <div class="md:col-span-1">
                                <label for="{{ $setting->key }}"
                                    class="block text-sm font-medium text-gray-700">{{ $setting->label }}</label>
                                <p class="text-xs text-gray-500 mt-1">Key: <span class="font-mono">{{ $setting->key }}</span></p>
                            </div>
                            <div class="md:col-span-2">
                                @if($setting->type === 'text' || $setting->type === 'number')
                                    <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                        value="{{ $setting->value }}"
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                @elseif($setting->type === 'textarea')
                                    <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="3"
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">{{ $setting->value }}</textarea>
                                @elseif($setting->type === 'boolean')
                                    <select name="{{ $setting->key }}" id="{{ $setting->key }}"
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                        <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Bật (True)</option>
                                        <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Tắt (False)</option>
                                    </select>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </form>

    </div>
@endsection
