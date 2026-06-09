<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaffRequest;
use App\Models\Role;
use App\Models\Staff;
use App\Services\Admin\StaffService;

class StaffController extends Controller
{
    protected StaffService $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * Display all staff.
     */
    public function index()
    {
        $staff = $this->staffService->getAll();

        return view('admin.staff.index', compact('staff'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $roles = Role::all();

        return view('admin.staff.create', compact('roles'));
    }

    /**
     * Store a new staff.
     */
    public function store(StaffRequest $request)
    {
        $this->staffService->create($request->validated());

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff account created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Staff $staff)
    {
        $roles = Role::all();

        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    /**
     * Update a staff.
     */
    public function update(StaffRequest $request, Staff $staff)
    {
        $this->staffService->update($staff, $request->validated());

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff account updated successfully.');
    }

    /**
     * Delete a staff.
     */
    public function destroy(Staff $staff)
    {
        // Prevent self-delete
        if ($staff->id === auth('staff')->id()) {
            return redirect()
                ->route('admin.staff.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $this->staffService->delete($staff);

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff account deleted successfully.');
    }
}