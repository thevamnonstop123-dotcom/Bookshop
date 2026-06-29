<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ProfileRequest;
use App\Services\Customer\ProfileService;
use App\Services\Customer\RatingService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected ProfileService $profileService;
    protected RatingService $ratingService;

    public function __construct(ProfileService $profileService, RatingService $ratingService)
    {
        $this->profileService = $profileService;
        $this->ratingService = $ratingService;
    }

    /**
     * Show profile page with optional tab.
     */
    public function index(Request $request)
    {
        $customer = auth('customer')->user();
        $addresses = $this->profileService->getAddresses($customer->id);
        $tab = $request->get('tab', 'personal');

        $reviews = collect();
        if ($tab === 'reviews') {
            $reviews = $this->ratingService->getCustomerReviews($customer->id);
        }

        return view('customer.profile.index', compact('customer', 'addresses', 'tab', 'reviews'));
    }

    /**
     * Update personal info (name, phone, dob, gender, image).
     */
    public function update(ProfileRequest $request)
    {
        $data = $request->only(['name', 'phone', 'gender', 'dob']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        $this->profileService->updateProfile(auth('customer')->user(), $data);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change email.
     */
    public function changeEmail(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'email' => 'required|email|max:100|unique:customers,email,' . auth('customer')->id(),
        ]);

        try {
            $this->profileService->changeEmail(
                auth('customer')->user(),
                $request->current_password,
                $request->email
            );
            return back()->with('success', 'Email updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $this->profileService->changePassword(
                auth('customer')->user(),
                $request->current_password,
                $request->password
            );
            return back()->with('success', 'Password changed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Upload profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $this->profileService->updatePhoto(
            auth('customer')->user(),
            $request->file('image')
        );

        return response()->json(['message' => 'Photo updated.', 'image_url' => asset('storage/' . $path)]);
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

        return back()->with('success', 'Address deleted.');
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