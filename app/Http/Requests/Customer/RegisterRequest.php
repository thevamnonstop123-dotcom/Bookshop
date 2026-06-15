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
            'name'     => 'required|string|min:2|max:100',
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
            // Name
            'name.required'     => 'Full name is required.',
            'name.min'          => 'Name must be at least 2 characters.',
            'name.max'          => 'Name cannot exceed 100 characters.',

            // Email
            'email.required'    => 'Email address is required.',
            'email.email'       => 'Please enter a valid email address (e.g., user@domain.com).',
            'email.unique'      => 'This email is already registered. Please login.',

            // Phone
            'phone.required'    => 'Phone number is required.',
            'phone.unique'      => 'This phone number is already registered.',
            'phone.regex'       => 'Phone number must be exactly 11 digits and start with 09 (e.g., 09123456789).',

            // Gender
            'gender.required'   => 'Please select your gender.',
            'gender.in'         => 'Gender must be male or female.',

            // Date of Birth
            'dob.required'      => 'Date of birth is required.',
            'dob.date'          => 'Please enter a valid date.',
            'dob.before'        => 'Date of birth must be before today.',

            // Password
            'password.required' => 'Password is required.',
            'password.min'      => 'Password must be at least 8 characters.',
            'password.confirmed'=> 'Password confirmation does not match.',
            'password.regex'    => 'Password must contain at least one letter and one number.',
        ];
    }
}