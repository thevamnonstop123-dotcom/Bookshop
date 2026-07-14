@extends('layouts.admin')

@section('title', 'Categories — Bookshop Admin')
@section('page_title', 'Category Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')
    <div class="admin-table-card">
        <div class="admin-table-header">
            <div class="admin-table-header-left">
                <h2 class="admin-table-title">All Categories</h2>
                <span class="admin-table-count">{{ $categories->count() }} {{ Str::plural('category', $categories->count()) }}</span>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="admin-btn admin-btn-primary">
                <i class="fas fa-plus"></i> Add Category
            </a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th style="width: 70px;">Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th style="width: 80px;">Books</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="admin-table-index">{{ $loop->iteration }}</td>
                            <td>
                                @if($category->image && $category->image !== 'default.png')
                                    <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" class="admin-table-thumb" style="width:60px;height:45px;object-fit:cover;">
                                @else
                                    <div style="width:60px;height:45px;background:var(--color-bg);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--color-text-muted);font-size:18px;">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="admin-table-name">{{ $category->name }}</div>
                            </td>
                            <td class="admin-table-bio">{{ Str::limit($category->description, 80) ?: '—' }}</td>
                            <td class="admin-table-number">{{ $category->books_count }}</td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $category->status === 'active' ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $category->status === 'active' ? 'circle-check' : 'circle-xmark' }}"></i>
                                    {{ ucfirst($category->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="admin-action-btn admin-action-edit" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                        onsubmit="return confirm('Delete this category? Books in this category will NOT be deleted.')">
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
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <h4>No categories found</h4>
                                    <p>Organize your books by creating categories.</p>
                                    <a href="{{ route('admin.categories.create') }}" class="admin-btn admin-btn-primary">
                                        <i class="fas fa-plus"></i> Add Category
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