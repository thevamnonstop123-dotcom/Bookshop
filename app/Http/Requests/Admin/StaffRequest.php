<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('staff')?->id;

        $rules = [
            'role_id' => 'required|exists:roles,id',
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:100|unique:staff,email,' . $staffId,
            'phone'   => 'required|regex:/^09[0-9]{9}$/|unique:staff,phone,' . $staffId,
            'address' => 'required|string|max:500',
            'gender'  => 'required|in:male,female',
            'dob'     => 'required|date|before:today',
            'status'  => 'required|in:active,inactive',
        ];

        // Password required on create, optional on update
        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|min:8';
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png|max:2048';
        } else {
            $rules['password'] = 'nullable|string|min:8';
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png|max:2048';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'phone.regex'  => 'Phone number must be exactly 11 digits and start with 09.',
        ];
    }
}