@extends('layouts.admin')

@section('title', 'Edit Role - Bookshop Admin')
@section('page_title', 'Edit Role')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
    <style>
        .permission-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .permission-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: var(--color-surface);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition-fast);
            border: 1.5px solid transparent;
        }
        .permission-item:hover {
            border-color: var(--color-border);
        }
        .permission-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--color-accent);
            cursor: pointer;
        }
        .permission-item label {
            cursor: pointer;
            font-size: var(--font-size-sm);
            font-weight: var(--weight-medium);
            color: var(--color-text);
        }
    </style>
@endpush

@section('content')

    <div class="form-container" style="max-width: 560px; background: var(--color-white); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Role Name</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $role->name) }}" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Permissions</label>
                <div class="permission-grid">
                    <div class="permission-item">
                        <input type="hidden" name="can_manage_books" value="0">
                        <input type="checkbox" id="can_manage_books" name="can_manage_books" value="1" {{ old('can_manage_books', $role->can_manage_books) ? 'checked' : '' }}>
                        <label for="can_manage_books">Manage Books</label>
                    </div>
                    <div class="permission-item">
                        <input type="hidden" name="can_manage_orders" value="0">
                        <input type="checkbox" id="can_manage_orders" name="can_manage_orders" value="1" {{ old('can_manage_orders', $role->can_manage_orders) ? 'checked' : '' }}>
                        <label for="can_manage_orders">Manage Orders</label>
                    </div>
                    <div class="permission-item">
                        <input type="hidden" name="can_manage_users" value="0">
                        <input type="checkbox" id="can_manage_users" name="can_manage_users" value="1" {{ old('can_manage_users', $role->can_manage_users) ? 'checked' : '' }}>
                        <label for="can_manage_users">Manage Users</label>
                    </div>
                    <div class="permission-item">
                        <input type="hidden" name="can_view_reports" value="0">
                        <input type="checkbox" id="can_view_reports" name="can_view_reports" value="1" {{ old('can_view_reports', $role->can_view_reports) ? 'checked' : '' }}>
                        <label for="can_view_reports">View Reports</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-10 mt-20">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Role
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>

@endsection