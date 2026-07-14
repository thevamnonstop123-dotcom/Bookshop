@extends('layouts.admin')

@section('title', 'Staff — Bookshop Admin')
@section('page_title', 'Staff Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')
    @if (session('error'))
        <div class="admin-alert admin-alert-error">
            <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="admin-table-card">
        <div class="admin-table-header">
            <div class="admin-table-header-left">
                <h2 class="admin-table-title">All Staff</h2>
                <span class="admin-table-count">{{ $staff->count() }} {{ Str::plural('member', $staff->count()) }}</span>
            </div>
            <a href="{{ route('admin.staff.create') }}" class="admin-btn admin-btn-primary">
                <i class="fas fa-plus"></i> Add Staff
            </a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th style="width: 56px;">Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th style="width: 130px;">Role</th>
                        <th style="width: 90px;">Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staff as $member)
                        <tr>
                            <td class="admin-table-index">{{ $loop->iteration }}</td>
                            <td>
                                @php
                                    $staffImg = ($member->image && $member->image !== 'default.png')
                                        ? asset('storage/' . $member->image)
                                        : null;
                                @endphp
                                @if ($staffImg)
                                    <img src="{{ $staffImg }}" alt="{{ $member->name }}" class="admin-table-avatar" loading="lazy">
                                @else
                                    <div class="admin-table-avatar-placeholder">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="admin-table-name">{{ $member->name }}</div>
                            </td>
                            <td class="admin-table-email">{{ $member->email }}</td>
                            <td>{{ $member->phone ?? '—' }}</td>
                            <td>
                                <span class="admin-badge admin-badge-info">
                                    <i class="fas fa-user-shield"></i>
                                    {{ $member->role?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $member->status === 'active' ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $member->status === 'active' ? 'circle-check' : 'circle-xmark' }}"></i>
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="{{ route('admin.staff.edit', $member) }}" class="admin-action-btn admin-action-edit" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    @if ($member->id !== auth('staff')->id())
                                        <form action="{{ route('admin.staff.destroy', $member) }}" method="POST"
                                              onsubmit="return confirm('Delete this staff account? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-action-btn admin-action-delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="admin-action-btn admin-action-self" title="This is you" style="cursor: default; opacity: 0.4;">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="admin-table-empty">
                                    <div class="admin-table-empty-icon">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <h4>No staff accounts found</h4>
                                    <p>Add staff members to help manage your bookstore.</p>
                                    <a href="{{ route('admin.staff.create') }}" class="admin-btn admin-btn-primary">
                                        <i class="fas fa-plus"></i> Add Staff
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