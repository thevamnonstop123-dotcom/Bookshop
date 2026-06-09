<?php

namespace App\Services\Admin;

use App\Models\Role;

class RoleService
{
    /**
     * Get all roles with staff count.
     */
    public function getAll()
    {
        return Role::withCount('staff')->get();
    }

    /**
     * Store a new role.
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update a role.
     */
    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role;
    }

    /**
     * Delete a role (only if no staff assigned).
     */
    public function delete(Role $role): bool
    {
        if ($role->staff()->count() > 0) {
            return false;
        }

        $role->delete();

        return true;
    }
}