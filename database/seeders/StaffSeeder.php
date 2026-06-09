<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;

class StaffSeeder extends Seeder
{
    /**
     * Seed the staff table with a default admin account.
     */
    public function run(): void
    {
        Staff::create([
            'role_id'  => 1,                      // Super Admin
            'name'     => 'Admin User',
            'email'    => 'admin@bookshop.com',
            'phone'    => '09123456789',
            'address'  => 'Yangon, Myanmar',
            'gender'   => 'male',
            'dob'      => '1990-01-01',
            'password' => Hash::make('password'),
            'image'    => 'default.png',
            'status'   => 'active',
        ]);
    }
}