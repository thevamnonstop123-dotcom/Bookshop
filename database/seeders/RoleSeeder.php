<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table.
     */
    public function run(): void
    {
        Role::create([
            'name'              => 'Super Admin',
            'can_manage_books'  => true,
            'can_manage_orders' => true,
            'can_manage_users'  => true,
            'can_view_reports'  => true,
        ]);

        Role::create([
            'name'              => 'Content Manager',
            'can_manage_books'  => true,
            'can_manage_orders' => false,
            'can_manage_users'  => false,
            'can_view_reports'  => false,
        ]);

        Role::create([
            'name'              => 'Order Manager',
            'can_manage_books'  => false,
            'can_manage_orders' => true,
            'can_manage_users'  => false,
            'can_view_reports'  => false,
        ]);
    }
}