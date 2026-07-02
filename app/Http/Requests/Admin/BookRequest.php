<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->title) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }
    }

    public function rules(): array
    {
        $bookId = $this->route('book')?->id;

        $rules = [
            'category_id'    => 'required|exists:categories,id',
            'title'          => 'required|string|min:3|max:100',
            'slug'           => 'required|string|max:255|unique:books,slug,' . $bookId,
            'isbn'           => 'required|string|max:50|unique:books,isbn,' . $bookId,
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'availability_status' => 'nullable|string|in:in_stock,low_stock,out_of_stock,coming_soon,pre_order,discontinued',
            'language'       => 'required|string|max:50',
            'published_date' => 'required|date',
            'description'    => 'nullable|string|max:5000',
            'status'         => 'required|in:active,inactive',
            'is_ebook'       => 'boolean',
            'ebook_file'     => 'nullable|file|mimes:pdf|max:20480',
            'author_ids'     => 'required|array|min:1',
            'author_ids.*'   => 'exists:authors,id',
            'sale_price'     => 'nullable|numeric|min:0|lt:price',
            'sale_starts_at' => 'nullable|date',
            'sale_ends_at'   => 'nullable|date|after_or_equal:sale_starts_at',
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = $this->has('is_ebook') && $this->is_ebook
                ? 'nullable|image|mimes:jpg,jpeg,png|max:2048'
                : 'required|image|mimes:jpg,jpeg,png|max:2048';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png|max:2048';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.min'          => 'Book title must be at least 3 characters.',
            'price.min'          => 'Price cannot be negative.',
            'stock_quantity.min' => 'Stock cannot be negative.',
            'author_ids.required'=> 'Please select at least one author.',
            'image.max'          => 'Book cover must be less than 2MB.',
            'image.mimes'        => 'Only .jpg, .jpeg, and .png files are allowed.',
            'ebook_file.max'     => 'PDF file must be less than 20MB.',
        ];
    }
}