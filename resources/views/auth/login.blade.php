@extends('layouts.app')

@section('title', 'Đăng nhập tài khoản')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100">

            {{-- Header --}}
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i data-lucide="log-in" class="w-6 h-6 text-indigo-600"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Chào mừng trở lại!
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Vui lòng đăng nhập để tiếp tục mua sắm
                </p>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="rounded-md shadow-sm space-y-4">
                    {{-- Email --}}
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="h-5 w-5 text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="appearance-none rounded-xl relative block w-full px-3 py-3 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                placeholder="Địa chỉ Email" value="{{ old('email') }}" autofocus>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label for="password" class="sr-only">Mật khẩu</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                            </div>
                            <input id="password" name="password" :type="show ? 'text' : 'password'"
                                autocomplete="current-password" required
                                class="appearance-none rounded-xl relative block w-full px-3 py-3 pl-10 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                placeholder="Mật khẩu">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-indigo-600">
                                <i x-show="!show" data-lucide="eye" class="h-5 w-5"></i>
                                <i x-show="show" data-lucide="eye-off" class="h-5 w-5" style="display: none;"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                </div>

                {{-- Remember & Forgot --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900 cursor-pointer">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}"
                                class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                                Quên mật khẩu?
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Submit Button --}}
                <div>
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
                        <i data-lucide="log-in" class="h-5 w-5 text-indigo-200"></i>
                        <span>Đăng nhập</span>
                    </button>
                </div>
            </form>

            {{-- Divider --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500 font-medium uppercase tracking-wider">Hoặc</span>
                </div>
            </div>

            {{-- Google Login --}}
            <div>
                <a href="{{ route('google.login') }}"
                    class="w-full flex justify-center items-center gap-3 py-3 px-4 border border-gray-300 text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-all transform hover:-translate-y-0.5">
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-1.01.68-2.31 1.09-3.71 1.09-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.13c-.22-.68-.35-1.44-.35-2.13 0-.69.13-1.45.35-2.13V7.03H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.97l3.66-2.84z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.66l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.03l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span>Tiếp tục với Google</span>
                </a>
            </div>

            {{-- Register Link --}}
            <div class="mt-6 text-center text-sm">
                <span class="text-gray-600">Chưa có tài khoản?</span>
                <a href="{{ route('register') }}"
                    class="font-bold text-indigo-600 hover:text-indigo-500 transition-colors ml-1">
                    Đăng ký ngay
                </a>
            </div>
        </div>
    </div>
@endsection
