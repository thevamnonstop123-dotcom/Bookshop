<?php

namespace App\Services\Admin;

use App\Models\Staff;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StaffService
{
    /**
     * Get all staff with roles.
     */
    public function getAll()
    {
        return Staff::with('role')
            ->latest()
            ->get();
    }

    /**
     * Store a new staff account.
     */
    public function create(array $data): Staff
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return Staff::create($data);
    }

    /**
     * Update a staff account.
     */
    public function update(Staff $staff, array $data): Staff
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($staff->image && $staff->image !== 'default.png') {
                Storage::disk('public')->delete($staff->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        // Only update password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $staff->update($data);

        return $staff;
    }

    /**
     * Delete a staff account.
     */
    public function delete(Staff $staff): void
    {
        if ($staff->image && $staff->image !== 'default.png') {
            Storage::disk('public')->delete($staff->image);
        }

        $staff->delete();
    }

    /**
     * Upload staff profile image.
     */
    private function uploadImage(UploadedFile $file): string
    {
        return $file->store('staff', 'public');
    }
}