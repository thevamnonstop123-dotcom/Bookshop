@extends('layouts.admin')

@section('title', 'Roles — Bookshop Admin')
@section('page_title', 'Role Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')
    @if (session('error'))
        <div class="admin-alert admin-alert-error" style="background: #FEF2F2; color: #991B1B; border-color: #FECACA;">
            <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="admin-table-card">
        <div class="admin-table-header">
            <div class="admin-table-header-left">
                <h2 class="admin-table-title">All Roles</h2>
                <span class="admin-table-count">{{ $roles->count() }} {{ Str::plural('role', $roles->count()) }}</span>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="admin-btn admin-btn-primary">
                <i class="fas fa-plus"></i> Add Role
            </a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Role Name</th>
                        <th style="width: 90px;">Books</th>
                        <th style="width: 90px;">Orders</th>
                        <th style="width: 90px;">Users</th>
                        <th style="width: 90px;">Reports</th>
                        <th style="width: 80px;">Staff</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td class="admin-table-index">{{ $loop->iteration }}</td>
                            <td>
                                <div class="admin-table-name">{{ $role->name }}</div>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $role->can_manage_books ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $role->can_manage_books ? 'check' : 'xmark' }}"></i>
                                    {{ $role->can_manage_books ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $role->can_manage_orders ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $role->can_manage_orders ? 'check' : 'xmark' }}"></i>
                                    {{ $role->can_manage_orders ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $role->can_manage_users ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $role->can_manage_users ? 'check' : 'xmark' }}"></i>
                                    {{ $role->can_manage_users ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $role->can_view_reports ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $role->can_view_reports ? 'check' : 'xmark' }}"></i>
                                    {{ $role->can_view_reports ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="admin-table-number">{{ $role->staff_count }}</td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="admin-action-btn admin-action-edit" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                          onsubmit="return confirm('Delete this role? Staff assigned to this role will be affected.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-action-btn admin-action-delete"
                                                title="{{ $role->staff_count > 0 ? 'Cannot delete — staff assigned' : 'Delete' }}"
                                                {{ $role->staff_count > 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="admin-table-empty">
                                    <div class="admin-table-empty-icon">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <h4>No roles found</h4>
                                    <p>Create roles to manage staff permissions.</p>
                                    <a href="{{ route('admin.roles.create') }}" class="admin-btn admin-btn-primary">
                                        <i class="fas fa-plus"></i> Create Role
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection