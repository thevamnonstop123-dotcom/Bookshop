@extends('layouts.admin')

@section('title', 'Customers - Bookshop Admin')
@section('page_title', 'Customer Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-container">
        <div class="table-header">
            <h2>All Customers</h2>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @php
                                $customerImg = ($customer->image && $customer->image !== 'default.png')
                                    ? asset('storage/' . $customer->image)
                                    : null;
                            @endphp
                            @if ($customerImg)
                                <img src="{{ $customerImg }}" alt="{{ $customer->name }}"
                                     style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: #0f172a; font-weight: 700; font-size: 14px;">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="font-semibold">{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td>
                            <span class="badge {{ $customer->status === 'active' ? 'badge-success' : ($customer->status === 'banned' ? 'badge-danger' : 'badge-warning') }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px; color: var(--color-text-muted);">
                            <i class="fas fa-users" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                            No customers found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection