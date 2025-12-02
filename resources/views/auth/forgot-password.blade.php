@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100">

            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i data-lucide="key-round" class="w-6 h-6 text-indigo-600"></i>
                </div>
                <h2 class="mt-6 text-2xl font-bold text-gray-900">
                    Quên mật khẩu?
                </h2>
                <p class="mt-4 text-sm text-gray-600">
                    Đừng lo lắng! Vui lòng nhập địa chỉ email bạn đã đăng ký, chúng tôi sẽ gửi liên kết để đặt lại mật khẩu
                    mới.
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
                @csrf

                <div>
                    <label for="email" class="sr-only">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required autofocus
                            class="appearance-none rounded-xl relative block w-full px-3 py-3 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Địa chỉ Email" value="{{ old('email') }}">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-200 transition-all">
                        Gửi liên kết đặt lại mật khẩu
                    </button>

                    <a href="{{ route('login') }}"
                        class="w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-all">
                        Quay lại đăng nhập
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
