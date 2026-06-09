<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title'        => 'required|string|max:100',
            'description'  => 'nullable|string|max:500',
            'display_order'=> 'required|integer|min:0',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'status'       => 'required|in:active,inactive',
        ];

        // Image required on create, optional on update
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
            'image.max'    => 'Banner image must be less than 2MB.',
            'image.mimes'  => 'Only .jpg, .jpeg, and .png files are allowed.',
        ];
    }
}