<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use App\Services\Admin\BannerService;

class BannerController extends Controller
{
    protected BannerService $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    /**
     * Display all banners.
     */
    public function index()
    {
        $banners = $this->bannerService->getAll();

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a new banner.
     */
    public function store(BannerRequest $request)
    {
        $this->bannerService->create($request->validated());

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update a banner.
     */
    public function update(BannerRequest $request, Banner $banner)
    {
        $this->bannerService->update($banner, $request->validated());

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner updated successfully.');
    }

    /**
     * Delete a banner.
     */
    public function destroy(Banner $banner)
    {
        $this->bannerService->delete($banner);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner deleted successfully.');
    }
}