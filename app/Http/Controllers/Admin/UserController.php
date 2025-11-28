<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->listing($request->all());
        $allUserIds = $this->userService->getAllIds($request->all());
        $roles = Role::all(); // Để hiển thị filter
        return view('admin.users.index', compact('users', 'roles', 'allUserIds'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('roles', 'addresses');

        $debug = [
            'module' => 'AdminUser',
            'action' => 'Show',
            'user_id' => $user->id
        ];

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'debug' => $debug
            ]);
        }

        return view('admin.users.show', [
            'user' => $user,
            'server_debug' => $debug
        ]);
    }

    public function update(Request $request, User $user)
    {
        // 1. Validate dữ liệu
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array', // Array các role name hoặc id
            'status' => 'required|in:active,inactive,banned',
            'avatar' => 'nullable|image|max:2048'
        ]);

        try {
            // 2. Gọi Service để update và ghi log
            $this->userService->updateUser($user, $data);

            return redirect()->route('admin.users.index')
                ->with('success', 'Cập nhật người dùng thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Không thể xóa tài khoản của chính mình.');
        }
        $user->delete();
        return back()->with('success', 'Đã xóa người dùng.');
    }
}
