@extends('layouts.admin')

@section('title', 'Categories - Bookshop Admin')
@section('page_title', 'Category Management')

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
            <h2>All Categories</h2>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-accent btn-sm">
                <i class="fas fa-plus"></i> Add Category
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Books</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-semibold">{{ $category->name }}</td>
                        <td>{{ Str::limit($category->description, 60) ?: '—' }}</td>
                        <td>{{ $category->books_count }}</td>
                        <td>
                            <span class="badge {{ $category->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-5">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('Delete this category? Books in this category will NOT be deleted.')">
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
                        <td colspan="6" class="text-center" style="padding: 40px; color: var(--color-text-muted);">
                            <i class="fas fa-layer-group" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                            No categories found. <a href="{{ route('admin.categories.create') }}">Add your first category</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection