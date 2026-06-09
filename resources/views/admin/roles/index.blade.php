@extends('layouts.admin')

@section('title', 'Roles - Bookshop Admin')
@section('page_title', 'Role Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="table-container">
        <div class="table-header">
            <h2>All Roles</h2>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-accent btn-sm">
                <i class="fas fa-plus"></i> Add Role
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Role Name</th>
                    <th>Books</th>
                    <th>Orders</th>
                    <th>Users</th>
                    <th>Reports</th>
                    <th>Staff</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-semibold">{{ $role->name }}</td>
                        <td>
                            <span class="badge {{ $role->can_manage_books ? 'badge-success' : 'badge-danger' }}">
                                {{ $role->can_manage_books ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $role->can_manage_orders ? 'badge-success' : 'badge-danger' }}">
                                {{ $role->can_manage_orders ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $role->can_manage_users ? 'badge-success' : 'badge-danger' }}">
                                {{ $role->can_manage_users ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $role->can_view_reports ? 'badge-success' : 'badge-danger' }}">
                                {{ $role->can_view_reports ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>{{ $role->staff_count }}</td>
                        <td>
                            <div class="d-flex gap-5">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                      onsubmit="return confirm('Delete this role?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" {{ $role->staff_count > 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px; color: var(--color-text-muted);">
                            <i class="fas fa-lock" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                            No roles found. <a href="{{ route('admin.roles.create') }}">Create your first role</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection