<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Services\Admin\RoleService;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->getAll();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create([
            'name'              => $request->name,
            'can_manage_books'  => $request->has('can_manage_books') ? 1 : 0,
            'can_manage_orders' => $request->has('can_manage_orders') ? 1 : 0,
            'can_manage_users'  => $request->has('can_manage_users') ? 1 : 0,
            'can_view_reports'  => $request->has('can_view_reports') ? 1 : 0,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->update([
            'name'              => $request->name,
            'can_manage_books'  => $request->has('can_manage_books') ? 1 : 0,
            'can_manage_orders' => $request->has('can_manage_orders') ? 1 : 0,
            'can_manage_users'  => $request->has('can_manage_users') ? 1 : 0,
            'can_view_reports'  => $request->has('can_view_reports') ? 1 : 0,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->staff()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete role with assigned staff members.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}