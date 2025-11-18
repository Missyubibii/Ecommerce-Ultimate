<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Define middleware for the controller (Laravel 11/12 Standard)
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:admin', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $filters = $request->all();

        // 1. Gọi Service để lấy danh sách (đã phân trang & lọc)
        $users = $this->userService->listing($filters);

        // 2. Lấy danh sách tất cả ID khớp với bộ lọc (để dùng cho nút "Chọn tất cả")
        $allUserIds = $this->userService->getAllIds($filters);

        // Lấy danh sách Roles cho dropdown lọc
        $roles = Role::all();

        $debug = [
            'module' => 'AdminUser',
            'action' => 'List',
            'filters' => $filters,
            'count' => $users->total()
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'users' => $users,
                'debug' => $debug
            ]);
        }

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
            'allUserIds' => $allUserIds,
            'server_debug' => $debug
        ]);
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

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,banned',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->update($validated);

        // Update roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        $debug = [
            'module' => 'AdminUser',
            'action' => 'Update',
            'user_id' => $user->id
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Người dùng đã được cập nhật thành công',
                'user' => $user,
                'debug' => $debug
            ]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'Người dùng đã được cập nhật thành công')
            ->with('server_debug', $debug);
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        $debug = [
            'module' => 'AdminUser',
            'action' => 'ResetPassword',
            'user_id' => $user->id
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mật khẩu đã được đặt lại thành công',
                'debug' => $debug
            ]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'Mật khẩu đã được đặt lại thành công')
            ->with('server_debug', $debug);
    }

    /**
     * Handle Bulk Delete
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $count = $this->userService->bulkDelete($request->ids);

        $debug = [
            'module' => 'AdminUser',
            'action' => 'BulkDelete',
            'count' => $count,
            'ids' => $request->ids
        ];

        return response()->json([
            'success' => true,
            'message' => "Đã xóa thành công $count người dùng.",
            'debug' => $debug
        ]);
    }
}
