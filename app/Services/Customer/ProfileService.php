<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Update customer profile.
     */
    public function updateProfile(Customer $customer, array $data): Customer
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($customer->image && $customer->image !== 'default.png') {
                Storage::disk('public')->delete($customer->image);
            }
            $data['image'] = $data['image']->store('customers', 'public');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $customer->update($data);

        return $customer;
    }

    /**
     * Get customer addresses.
     */
    public function getAddresses(int $customerId)
    {
        return CustomerAddress::where('customer_id', $customerId)->latest()->get();
    }

    /**
     * Store a new address.
     */
    public function storeAddress(int $customerId, array $data): CustomerAddress
    {
        // If this is the default, unset others
        if (!empty($data['is_default'])) {
            CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
        }

        $data['customer_id'] = $customerId;

        return CustomerAddress::create($data);
    }

    /**
     * Update an address.
     */
    public function updateAddress(int $addressId, array $data): CustomerAddress
    {
        $address = CustomerAddress::findOrFail($addressId);

        if (!empty($data['is_default'])) {
            CustomerAddress::where('customer_id', $address->customer_id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return $address;
    }

    /**
     * Delete an address.
     */
    public function deleteAddress(int $addressId): void
    {
        $address = CustomerAddress::findOrFail($addressId);
        $address->delete();
    }

    /**
     * Set default address.
     */
    public function setDefault(int $addressId, int $customerId): void
    {
        CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
        CustomerAddress::where('id', $addressId)->update(['is_default' => true]);
    }
}