@extends('layouts.admin')

@section('title', 'Customers — Bookshop Admin')
@section('page_title', 'Customer Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')

    @if (session('success'))
        <div class="admin-alert admin-alert-success">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="admin-table-card">
        <div class="admin-table-header">
            <div class="admin-table-header-left">
                <h2 class="admin-table-title">All Customers</h2>
                <span class="admin-table-count">{{ $customers->count() }} {{ Str::plural('customer', $customers->count()) }}</span>
            </div>
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
                        <th style="width: 80px;">Orders</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="admin-table-index">{{ $loop->iteration }}</td>
                            <td>
                                @php
                                    $customerImg = ($customer->image && $customer->image !== 'default.png')
                                        ? asset('storage/' . $customer->image)
                                        : null;
                                @endphp
                                @if ($customerImg)
                                    <img src="{{ $customerImg }}" alt="{{ $customer->name }}" class="admin-table-avatar" loading="lazy">
                                @else
                                    <div class="admin-table-avatar-placeholder">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="admin-table-name">{{ $customer->name }}</div>
                            </td>
                            <td class="admin-table-email">{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '—' }}</td>
                            <td class="admin-table-number">{{ $customer->orders_count }}</td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $customer->status === 'active' ? 'success' : ($customer->status === 'banned' ? 'danger' : 'warning') }}">
                                    <i class="fas fa-{{ $customer->status === 'active' ? 'circle-check' : ($customer->status === 'banned' ? 'circle-xmark' : 'circle-pause') }}"></i>
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer) }}" class="admin-action-btn admin-action-view" title="View details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="admin-table-empty">
                                    <div class="admin-table-empty-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h4>No customers found</h4>
                                    <p>Customers will appear here once they register.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection