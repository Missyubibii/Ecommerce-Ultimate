<?php
// app/Http/Controllers/Auth/GoogleAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Tìm user theo email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Nếu user đã tồn tại, cập nhật avatar nếu chưa có (ưu tiên lấy từ google nếu user chưa có ảnh local)
                if (!$user->avatar) {
                    $user->update(['avatar' => $googleUser->getAvatar()]);
                }
                
                // Nếu user chưa verify email
                if (!$user->email_verified_at) {
                    $user->update(['email_verified_at' => now()]);
                }

                Auth::login($user, true);
            } else {
                // Nếu chưa có, tạo user mới
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'status' => 'active',
                ]);

                // Gán role mặc định là user (yêu cầu Spatie Permission)
                try {
                    $user->assignRole('user');
                } catch (Exception $e) {
                    \Log::warning('Could not assign customer role to Google user: ' . $e->getMessage());
                }

                Auth::login($user, true);
            }

            // Chuyển hướng dựa trên Role
            return $this->redirectBasedOnRole($user);

        } catch (Exception $e) {
            \Log::error('Google Login Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Có lỗi xảy ra khi đăng nhập bằng Google. Vui lòng thử lại.');
        }
    }

    /**
     * Chuyển hướng người dùng dựa trên Role sau khi đăng nhập
     */
    protected function redirectBasedOnRole($user)
    {
        // 1. Kiểm tra nếu là Admin hoặc Manager -> Ưu tiên vào trang quản trị
        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Chào mừng Quản trị viên quay trở lại!');
        }

        // 2. Nếu là Customer
        // Kiểm tra xem trang 'intended' có phải là trang admin không. 
        // Nếu có, xóa intended để redirect về trang chủ (tránh lỗi 403/404)
        $intendedUrl = session()->get('url.intended');
        if ($intendedUrl && str_contains($intendedUrl, '/admin')) {
            session()->forget('url.intended');
        }

        return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
    }
}
