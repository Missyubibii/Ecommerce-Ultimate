<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    //Khai báo property cartService
    protected $cartService;

    //Khởi tạo trong hàm khởi tạo
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {

        // 1. Lưu lại Session ID hiện tại (nơi chứa giỏ hàng Guest) TRƯỚC KHI bị thay đổi
        $previousSessionId = $request->session()->getId();

        $request->authenticate();

        // 2. Sau khi đăng nhập thành công - Chuyển các item từ session cũ sang user vừa đăng nhập
        if ($request->user()) {
            $this->cartService->mergeCart($previousSessionId, $request->user()->id);
        }

        // 3. Sau khi merge xong mới regenerate session (bảo mật)
        $request->session()->regenerate();

        if ($request->user()->hasRole('admin')) {
            // Nếu là Admin -> Về Dashboard quản trị
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }
        // Các trường hợp còn lại (Customer) -> Về Trang chủ
        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
