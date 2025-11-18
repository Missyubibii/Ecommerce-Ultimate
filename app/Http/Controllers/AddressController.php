<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Store a new address.
     */
    public function store(Request $request)
    {
        // 1. Validate
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

        // 2. Call Service
        $address = $this->userService->createAddress($request->user(), $data);

        // 3. Debug Metadata [cite: 28]
        $debug = [
            'module' => 'Address',
            'action' => 'Create',
            'address_id' => $address->id
        ];

        // 4. Hybrid Response [cite: 35]
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $address, 'debug' => $debug]);
        }

        return back()->with('status', 'address-created')->with('server_debug', $debug);
    }

    /**
     * Update an address.
     */
    public function update(Request $request, Address $address)
    {
        // Authorize: Ensure user owns this address
        if ($request->user()->id !== $address->user_id) {
            abort(403);
        }

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

        $updatedAddress = $this->userService->updateAddress($address, $data);

        $debug = [
            'module' => 'Address',
            'action' => 'Update',
            'address_id' => $updatedAddress->id
        ];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $updatedAddress, 'debug' => $debug]);
        }

        return back()->with('status', 'address-updated')->with('server_debug', $debug);
    }

    /**
     * Delete an address.
     */
    public function destroy(Request $request, Address $address)
    {
        if ($request->user()->id !== $address->user_id) {
            abort(403);
        }

        $this->userService->deleteAddress($address);

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
