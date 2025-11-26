<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected UserService $userService;

    // Inject Service vào Constructor
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        // Eager load addresses cho view
        $addresses = $user->addresses;

        $debug = [
            'module' => 'User',
            'action' => 'EditProfile',
            'user_id' => $user->id,
            'address_count' => $addresses->count()
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'addresses' => $addresses
                ],
                'debug' => $debug
            ]);
        }

        return view('profile.edit', [
            'user' => $user,
            'addresses' => $addresses,
            'server_debug' => $debug,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        // 1. Lấy dữ liệu đã validate (chỉ gồm name, email, phone)
        $data = $request->validated();

        // 2. Xử lý Avatar riêng (vì avatar có thể nullable trong validate nhưng có file gửi lên)
        if ($request->hasFile('avatar')) {
            $path = $this->userService->uploadAvatar($user, $request->file('avatar'));

            // Gán đường dẫn vào mảng data để update
            $data['avatar'] = $path;
        }

        // 3. Reset email verification nếu đổi email
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 4. Gọi Service update
        $this->userService->updateProfile($user, $data);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Có thể chuyển logic delete vào Service nếu muốn strictly layered
        // $this->userService->deleteUser($user);
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Account deleted']);
        }

        return Redirect::to('/');
    }
}
