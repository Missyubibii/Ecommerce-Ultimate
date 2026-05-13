@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100">

            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-rose-100 rounded-full flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-6 h-6 text-rose-600"></i>
                </div>
                <h2 class="mt-6 text-2xl font-bold text-gray-900">
                    Đặt lại mật khẩu mới
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Vui lòng nhập mật khẩu mới để bảo vệ tài khoản của bạn
                </p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="rounded-md shadow-sm space-y-4">
                    {{-- Email Address (Hidden but present for validation) --}}
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="h-5 w-5 text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="username" required readonly
                                class="appearance-none rounded-xl relative block w-full px-3 py-3 pl-10 border border-gray-200 bg-gray-50 text-gray-500 sm:text-sm"
                                placeholder="Địa chỉ Email" value="{{ old('email', $request->email) }}">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label for="password" class="sr-only">Mật khẩu mới</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                            </div>
                            <input id="password" name="password" :type="show ? 'text' : 'password'"
                                autocomplete="new-password" required autofocus
                                class="appearance-none rounded-xl relative block w-full px-3 py-3 pl-10 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-rose-500 focus:border-rose-500 focus:z-10 sm:text-sm"
                                placeholder="Mật khẩu mới">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-rose-600">
                                <i x-show="!show" data-lucide="eye" class="h-5 w-5"></i>
                                <i x-show="show" data-lucide="eye-off" class="h-5 w-5" style="display: none;"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="sr-only">Nhập lại mật khẩu mới</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="check-circle" class="h-5 w-5 text-gray-400"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" required
                                class="appearance-none rounded-xl relative block w-full px-3 py-3 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-rose-500 focus:border-rose-500 focus:z-10 sm:text-sm"
                                placeholder="Nhập lại mật khẩu mới">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 shadow-lg shadow-rose-200 transition-all transform hover:-translate-y-0.5">
                        <i data-lucide="save" class="h-5 w-5 text-rose-200"></i>
                        <span>Đặt lại mật khẩu</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
