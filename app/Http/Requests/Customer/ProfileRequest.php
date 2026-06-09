<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = auth('customer')->id();

        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:customers,email,' . $customerId,
            'phone'    => 'required|regex:/^09[0-9]{9}$/|unique:customers,phone,' . $customerId,
            'gender'   => 'required|in:male,female',
            'dob'      => 'required|date|before:today',
            'password' => 'nullable|string|min:8',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}