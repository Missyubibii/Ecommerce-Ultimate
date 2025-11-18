<?php

namespace App\Services;

use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->fill(collect($data)->only(['name', 'phone', 'avatar'])->toArray());
        $user->save();
        return $user->fresh();
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
        // If this is set as default, unset other default addresses
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
        // If this is set as default, unset other default addresses
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
            // Reset all other addresses for this user
            $address->user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);

            // Set this address as default
            $address->update(['is_default' => true]);
        });

        return $address;
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(User $user, $avatar): string
    {
        // Delete old avatar if exists
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
        // Loại bỏ ID của chính người đang đăng nhập
        $ids = array_diff($ids, [auth()->id()]);

        return User::whereIn('id', $ids)->delete();
    }
}
