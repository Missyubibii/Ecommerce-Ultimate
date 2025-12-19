<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Thêm địa chỉ mới.
     */
    public function store(Request $request)
    {
        // 1. Kiểm tra dữ liệu
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'boolean'
        ]);

        // 2. Gọi Service
        $address = $this->userService->createAddress($request->user(), $data);

        // 3. Debug Metadata
        $debug = [
            'module' => 'Address',
            'action' => 'Create',
            'address_id' => $address->id
        ];

        // 4. Trả về JSON
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $address, 'debug' => $debug]);
        }

        // 5. Trả về view
        return back()->with('status', 'address-created')->with('server_debug', $debug);
    }

    /**
     * Cập nhật địa chỉ.
     */
    public function update(Request $request, Address $address)
    {
        // 1. Kiểm tra quyền
        if ($request->user()->id !== $address->user_id) {
            abort(403);
        }

        // 2. Kiểm tra dữ liệu
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'boolean'
        ]);

        // 3. Gọi Service
        $updatedAddress = $this->userService->updateAddress($address, $data);

        // 4. Debug Metadata
        $debug = [
            'module' => 'Address',
            'action' => 'Update',
            'address_id' => $updatedAddress->id
        ];

        // 5. Trả về JSON
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $updatedAddress, 'debug' => $debug]);
        }

        // 6. Trả về view
        return back()->with('status', 'address-updated')->with('server_debug', $debug);
    }

    /**
     * Xóa địa chỉ.
     */
    public function destroy(Request $request, Address $address)
    {
        // 1. Kiểm tra quyền
        if ($request->user()->id !== $address->user_id) {
            abort(403);
        }

        // 2. Gọi Service
        $this->userService->deleteAddress($address);

        // 3. Trả về Json
        $debug = ['module' => 'Address', 'action' => 'Delete', 'id' => $address->id];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'debug' => $debug]);
        }

        return back()->with('status', 'address-deleted')->with('server_debug', $debug);
    }

    /**
     * Set address as default.
     */
    public function setDefault(Request $request, Address $address)
    {
        if ($request->user()->id !== $address->user_id) {
            abort(403);
        }

        $this->userService->setDefaultAddress($address);

        $debug = ['module' => 'Address', 'action' => 'SetDefault', 'id' => $address->id];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'debug' => $debug]);
        }

        return back()->with('server_debug', $debug);
    }
}

