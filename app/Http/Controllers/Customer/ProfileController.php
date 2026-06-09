<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ProfileRequest;
use App\Services\Customer\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Show profile page.
     */
    public function index()
    {
        $customer = auth('customer')->user();
        $addresses = $this->profileService->getAddresses($customer->id);

        return view('customer.profile.index', compact('customer', 'addresses'));
    }

    /**
     * Update profile.
     */
    public function update(ProfileRequest $request)
    {
        $this->profileService->updateProfile(auth('customer')->user(), $request->validated());

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Store a new address.
     */
    public function storeAddress(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:100',
            'phone_number'  => 'required|regex:/^09[0-9]{9}$/',
            'address_line'  => 'required|string|max:500',
        ]);

        $this->profileService->storeAddress(auth('customer')->id(), $request->all());

        return back()->with('success', 'Address added successfully.');
    }

    /**
     * Update an address.
     */
    public function updateAddress(Request $request, $addressId)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:100',
            'phone_number'  => 'required|regex:/^09[0-9]{9}$/',
            'address_line'  => 'required|string|max:500',
        ]);

        $this->profileService->updateAddress($addressId, $request->all());

        return back()->with('success', 'Address updated successfully.');
    }

    /**
     * Delete an address.
     */
    public function deleteAddress($addressId)
    {
        $this->profileService->deleteAddress($addressId);

        return back()->with('success', 'Address deleted successfully.');
    }

    /**
     * Set default address.
     */
    public function setDefaultAddress($addressId)
    {
        $this->profileService->setDefault($addressId, auth('customer')->id());

        return back()->with('success', 'Default address updated.');
    }
}