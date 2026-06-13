@extends('layouts.admin')

@section('title', 'Edit Role — Bookshop Admin')
@section('page_title', 'Edit Role')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <a href="{{ route('admin.roles.index') }}" class="admin-form-back">
        <i class="fas fa-arrow-left"></i> Back to Roles
    </a>

    <div class="admin-form-card">
        <div class="admin-form-card-header">
            <div class="admin-form-card-icon">
                <i class="fas fa-key"></i>
            </div>
            <div>
                <h2 class="admin-form-card-title">Edit Role</h2>
                <p class="admin-form-card-subtitle">Update permissions for <strong>{{ $role->name }}</strong></p>
            </div>
        </div>

        <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="admin-form">
            @csrf
            @method('PUT')

            <div class="admin-form-grid">
                {{-- Role Name --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="name" class="admin-form-label">
                        Role Name <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-tag admin-form-input-icon"></i>
                        <input type="text" id="name" name="name"
                               class="admin-form-input @error('name') admin-form-input-error @enderror"
                               value="{{ old('name', $role->name) }}" required>
                    </div>
                    @error('name')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Permissions --}}
                <div class="admin-form-group admin-form-group-full">
                    <label class="admin-form-label">Permissions</label>
                    <div class="permission-grid">
                        <label class="permission-card {{ old('can_manage_books', $role->can_manage_books) ? 'permission-card-selected' : '' }}">
                            <input type="hidden" name="can_manage_books" value="0">
                            <input type="checkbox" name="can_manage_books" value="1"
                                   {{ old('can_manage_books', $role->can_manage_books) ? 'checked' : '' }}>
                            <div class="permission-card-content">
                                <div class="permission-card-icon permission-icon-books">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="permission-card-info">
                                    <span class="permission-card-title">Manage Books</span>
                                    <span class="permission-card-desc">Add, edit, delete books and authors</span>
                                </div>
                                <div class="permission-card-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </label>

                        <label class="permission-card {{ old('can_manage_orders', $role->can_manage_orders) ? 'permission-card-selected' : '' }}">
                            <input type="hidden" name="can_manage_orders" value="0">
                            <input type="checkbox" name="can_manage_orders" value="1"
                                   {{ old('can_manage_orders', $role->can_manage_orders) ? 'checked' : '' }}>
                            <div class="permission-card-content">
                                <div class="permission-card-icon permission-icon-orders">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <div class="permission-card-info">
                                    <span class="permission-card-title">Manage Orders</span>
                                    <span class="permission-card-desc">View and update order statuses</span>
                                </div>
                                <div class="permission-card-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </label>

                        <label class="permission-card {{ old('can_manage_users', $role->can_manage_users) ? 'permission-card-selected' : '' }}">
                            <input type="hidden" name="can_manage_users" value="0">
                            <input type="checkbox" name="can_manage_users" value="1"
                                   {{ old('can_manage_users', $role->can_manage_users) ? 'checked' : '' }}>
                            <div class="permission-card-content">
                                <div class="permission-card-icon permission-icon-users">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="permission-card-info">
                                    <span class="permission-card-title">Manage Users</span>
                                    <span class="permission-card-desc">Manage customers, staff, and roles</span>
                                </div>
                                <div class="permission-card-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </label>

                        <label class="permission-card {{ old('can_view_reports', $role->can_view_reports) ? 'permission-card-selected' : '' }}">
                            <input type="hidden" name="can_view_reports" value="0">
                            <input type="checkbox" name="can_view_reports" value="1"
                                   {{ old('can_view_reports', $role->can_view_reports) ? 'checked' : '' }}>
                            <div class="permission-card-content">
                                <div class="permission-card-icon permission-icon-reports">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="permission-card-info">
                                    <span class="permission-card-title">View Reports</span>
                                    <span class="permission-card-desc">Access payment and sales reports</span>
                                </div>
                                <div class="permission-card-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-check"></i> Update Role
                </button>
                <a href="{{ route('admin.roles.index') }}" class="admin-btn admin-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

@endsection