<?php

namespace App\Services\Admin;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    /**
     * Get all banners ordered by display_order.
     */
    public function getAll()
    {
        return Banner::orderBy('display_order')->get();
    }

    /**
     * Store a new banner.
     */
    public function create(array $data): Banner
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        $data['created_by'] = auth('staff')->id();
        $data['updated_by'] = auth('staff')->id();

        return Banner::create($data);
    }

    /**
     * Update an existing banner.
     */
    public function update(Banner $banner, array $data): Banner
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        $data['updated_by'] = auth('staff')->id();

        $banner->update($data);

        return $banner;
    }

    /**
     * Delete a banner.
     */
    public function delete(Banner $banner): void
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();
    }

    /**
     * Upload banner image to storage.
     */
    private function uploadImage(UploadedFile $file): string
    {
        return $file->store('banners', 'public');
    }
}