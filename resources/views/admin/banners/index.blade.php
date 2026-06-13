@extends('layouts.admin')

@section('title', 'Banners — Bookshop Admin')
@section('page_title', 'Banner Management')

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
                <h2 class="admin-table-title">All Banners</h2>
                <span class="admin-table-count">{{ $banners->count() }} {{ Str::plural('banner', $banners->count()) }}</span>
            </div>
            <a href="{{ route('admin.banners.create') }}" class="admin-btn admin-btn-primary">
                <i class="fas fa-plus"></i> Add Banner
            </a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th style="width: 120px;">Image</th>
                        <th>Title</th>
                        <th style="width: 100px;">Order</th>
                        <th style="width: 200px;">Period</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($banners as $banner)
                        <tr>
                            <td class="admin-table-index">{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $banner->image) }}"
                                     alt="{{ $banner->title }}"
                                     class="admin-table-thumb"
                                     loading="lazy">
                            </td>
                            <td>
                                <div class="admin-table-name">{{ $banner->title }}</div>
                                @if($banner->description)
                                    <div class="admin-table-meta">{{ Str::limit($banner->description, 60) }}</div>
                                @endif
                            </td>
                            <td class="admin-table-number">{{ $banner->display_order }}</td>
                            <td>
                                <div class="admin-table-dates">
                                    <span><i class="fas fa-calendar"></i> {{ $banner->start_date->format('d M Y') }}</span>
                                    <span class="admin-table-dates-separator">to</span>
                                    <span><i class="fas fa-calendar"></i> {{ $banner->end_date->format('d M Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $banner->status === 'active' ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $banner->status === 'active' ? 'circle-check' : 'circle-xmark' }}"></i>
                                    {{ ucfirst($banner->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="admin-action-btn admin-action-edit" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                                          onsubmit="return confirm('Delete this banner? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-action-btn admin-action-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="admin-table-empty">
                                    <div class="admin-table-empty-icon">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <h4>No banners found</h4>
                                    <p>Create promotional banners to display on your homepage hero slider.</p>
                                    <a href="{{ route('admin.banners.create') }}" class="admin-btn admin-btn-primary">
                                        <i class="fas fa-plus"></i> Add Banner
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