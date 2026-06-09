@extends('layouts.admin')

@section('title', 'Banners - Bookshop Admin')
@section('page_title', 'Banner Management')

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
            <h2>All Banners</h2>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-accent btn-sm">
                <i class="fas fa-plus"></i> Add Banner
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Display Order</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($banners as $banner)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <img src="{{ asset('storage/' . $banner->image) }}"
                                 alt="{{ $banner->title }}"
                                 style="width: 80px; height: 40px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td>{{ $banner->title }}</td>
                        <td>{{ $banner->display_order }}</td>
                        <td>{{ $banner->start_date->format('d M Y') }} - {{ $banner->end_date->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $banner->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($banner->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-5">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                                      onsubmit="return confirm('Delete this banner?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 40px; color: var(--color-text-muted);">
                            <i class="fas fa-image" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                            No banners found. <a href="{{ route('admin.banners.create') }}">Add your first banner</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection