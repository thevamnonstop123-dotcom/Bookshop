<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'   => 'required|string|max:100',
            'bio'    => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = 'required|image|mimes:jpg,jpeg,png|max:2048';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png|max:2048';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'image.max'   => 'Author image must be less than 2MB.',
            'image.mimes' => 'Only .jpg, .jpeg, and .png files are allowed.',
        ];
    }
}