<?php

namespace App\Services;

use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $activityLogService;

    // [QUAN TRỌNG] Inject ActivityLogService
    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * [ADMIN ONLY] Cập nhật User đầy đủ (Info + Roles + Status)
     * Hàm này dùng cho Admin Controller để ghi log thay đổi quyền hạn
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Cập nhật thông tin cơ bản
            // Trait LogsActivity trong Model sẽ tự động ghi log các trường này
            $fillableData = collect($data)->only(['name', 'email', 'phone', 'status'])->toArray();

            // Nếu có nhập password mới
            if (!empty($data['password'])) {
                $fillableData['password'] = Hash::make($data['password']);
            }

            $user->fill($fillableData);

            // Xử lý Avatar nếu có upload file mới
            if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $data['avatar']->store('avatars', 'public');
            }

            if ($user->isDirty()) {
                $user->save();
            }

            // 2. Xử lý Role (Cần Log thủ công vì Model Event không bắt được thay đổi bảng pivot)
            if (isset($data['roles'])) {
                $oldRoles = $user->getRoleNames()->toArray();

                // Sync roles (spatie method)
                $user->syncRoles($data['roles']);

                $newRoles = $user->getRoleNames()->toArray();

                // So sánh và ghi log nếu khác biệt
                if ($oldRoles !== $newRoles) {
                    $this->activityLogService->logAction(
                        'Cập nhật quyền hạn (Roles)',
                        $user,
                        'updated',
                        [
                            'old' => $oldRoles,
                            'new' => $newRoles
                        ]
                    );
                }
            }

            return $user;
        });
    }

    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data): User
    {
        $fillableData = collect($data)->only(['name', 'email', 'phone'])->toArray(); // Bỏ avatar ra xử lý riêng nếu cần

        // Nếu data có avatar là file upload
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
             $this->uploadAvatar($user, $data['avatar']);
        }

        $user->fill($fillableData);
        $user->save();

        return $user;
    }

    /**
     * Update user password
     */
    public function updatePassword(User $user, string $password): bool
    {
        return $user->update([
            'password' => Hash::make($password)
        ]);
    }
    /**
     * Create a new address for the user
     */
    public function createAddress(User $user, array $data): Address
    {
        if (isset($data['is_default']) && $data['is_default']) {
            $user->addresses()->update(['is_default' => false]);
        }
        return $user->addresses()->create($data);
    }

    /**
     * Update an existing address
     */
    public function updateAddress(Address $address, array $data): Address
    {
        if (isset($data['is_default']) && $data['is_default']) {
            $address->user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }
        $address->update($data);
        return $address;
    }

    /**
     * Delete an address
     */
    public function deleteAddress(Address $address): bool
    {
        return $address->delete();
    }

    /**
     * Set an address as default
     */
    public function setDefaultAddress(Address $address): Address
    {
        DB::transaction(function () use ($address) {
            $address->user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });
        return $address;
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(User $user, $avatar): string
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $path = $avatar->store('avatars', 'public');
        $user->update(['avatar' => $path]);
        return $path;
    }

    /**
     * Helper: Build Query Filter
     */
    private function buildFilterQuery(array $filters)
    {
        $query = User::with('roles');

        // Filter: Search Keyword
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['q']}%")
                    ->orWhere('email', 'like', "%{$filters['q']}%")
                    ->orWhere('phone', 'like', "%{$filters['q']}%");
            });
        }

        // Filter: Role
        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        // Filter: Status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Get Paginated Users
     */
    public function listing(array $filters, int $perPage = 10)
    {
        return $this->buildFilterQuery($filters)->latest()->paginate($perPage);
    }

    /**
     * Get All IDs for Bulk Actions
     */
    public function getAllIds(array $filters)
    {
        return $this->buildFilterQuery($filters)->pluck('id')->toArray();
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(array $ids): int
    {
        $ids = array_diff($ids, [Auth::id()]);
        return User::whereIn('id', $ids)->delete();
    }
}
