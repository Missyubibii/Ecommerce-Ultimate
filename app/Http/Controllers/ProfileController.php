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
        $data = $request->validated();

        // Logic reset email verification nếu đổi email (giữ lại từ Breeze)
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Xử lý upload Avatar trước khi gọi Service update info
        if ($request->hasFile('avatar')) {
            // Gọi hàm uploadAvatar trong service để lấy path
            $path = $this->userService->uploadAvatar($user, $request->file('avatar'));
            $data['avatar'] = $path;
        }

        $updatedUser = $this->userService->updateProfile($user, $data);

        // Debug Data
        $debug = [
            'module' => 'User',
            'action' => 'UpdateProfile',
            'changes' => $updatedUser->getChanges() // Track changes
        ];

        // Hybrid Response
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $updatedUser, 'debug' => $debug]);
        }

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('server_debug', $debug);
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
