@extends('layouts.admin')

@section('title', 'Staff - Bookshop Admin')
@section('page_title', 'Staff Management')

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
            <h2>All Staff</h2>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-accent btn-sm">
                <i class="fas fa-plus"></i> Add Staff
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staff as $member)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @php
                                $staffImg = ($member->image && $member->image !== 'default.png')
                                    ? asset('storage/' . $member->image)
                                    : null;
                            @endphp
                            @if ($staffImg)
                                <img src="{{ $staffImg }}" alt="{{ $member->name }}"
                                     style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: #0f172a; font-weight: 700; font-size: 14px;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="font-semibold">{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->phone }}</td>
                        <td>
                            <span class="badge badge-info">{{ $member->role?->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $member->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-5">
                                <a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if ($member->id !== auth('staff')->id())
                                    <form action="{{ route('admin.staff.destroy', $member) }}" method="POST"
                                          onsubmit="return confirm('Delete this staff account?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px; color: var(--color-text-muted);">
                            <i class="fas fa-user-shield" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                            No staff accounts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection