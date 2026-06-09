<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:customers,email',
            'phone'    => [
                'required',
                'unique:customers,phone',
                'regex:/^09[0-9]{9}$/',
            ],
            'gender'   => 'required|in:male,female',
            'dob'      => 'required|date|before:today',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex'       => 'Phone number must be exactly 11 digits and start with 09 (e.g., 09123456789).',
            'phone.unique'      => 'This phone number is already registered.',
            'gender.required'   => 'Please select your gender.',
            'gender.in'         => 'Gender must be male or female.',
            'dob.required'      => 'Please enter your date of birth.',
            'dob.before'        => 'Date of birth must be in the past.',
            'password.min'      => 'Password must be at least 8 characters.',
            'password.regex'    => 'Password must contain at least one letter and one number.',
        ];
    }
}