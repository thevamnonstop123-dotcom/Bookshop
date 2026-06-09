<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id;

        return [
            'name'              => 'required|string|max:50|unique:roles,name,' . $roleId,
            'can_manage_books'  => 'boolean',
            'can_manage_orders' => 'boolean',
            'can_manage_users'  => 'boolean',
            'can_view_reports'  => 'boolean',
        ];
    }
}