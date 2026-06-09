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

    /**
     * Display all roles.
     */
    public function index()
    {
        $roles = $this->roleService->getAll();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a new role.
     */
    public function store(RoleRequest $request)
    {
        $this->roleService->create($request->validated());

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update a role.
     */
    public function update(RoleRequest $request, Role $role)
    {
        $this->roleService->update($role, $request->validated());

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role)
    {
        $deleted = $this->roleService->delete($role);

        if (!$deleted) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Cannot delete role with assigned staff members.');
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}