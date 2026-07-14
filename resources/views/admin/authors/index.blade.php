@extends('layouts.admin')

@section('title', 'Authors — Bookshop Admin')
@section('page_title', 'Author Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')


    <div class="admin-table-card">
        <div class="admin-table-header">
            <div class="admin-table-header-left">
                <h2 class="admin-table-title">All Authors</h2>
                <span class="admin-table-count">{{ $authors->count() }} {{ Str::plural('author', $authors->count()) }}</span>
            </div>
            <a href="{{ route('admin.authors.create') }}" class="admin-btn admin-btn-primary">
                <i class="fas fa-plus"></i> Add Author
            </a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th style="width: 60px;">Photo</th>
                        <th>Name</th>
                        <th>Biography</th>
                        <th style="width: 80px;">Books</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($authors as $author)
                        <tr>
                            <td class="admin-table-index">{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ $author->image ? asset('storage/' . $author->image) : 'https://ui-avatars.com/api/?name=' . urlencode($author->name) . '&background=10B981&color=fff&size=40' }}"
                                     alt="{{ $author->name }}"
                                     class="admin-table-avatar"
                                     loading="lazy">
                            </td>
                            <td class="admin-table-name">{{ $author->name }}</td>
                            <td class="admin-table-bio">{{ Str::limit($author->bio, 80) ?: '—' }}</td>
                            <td class="admin-table-number">{{ $author->books_count }}</td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $author->status === 'active' ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $author->status === 'active' ? 'circle-check' : 'circle-xmark' }}"></i>
                                    {{ ucfirst($author->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="{{ route('admin.authors.edit', $author) }}" class="admin-action-btn admin-action-edit" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.authors.destroy', $author) }}" method="POST"
                                          onsubmit="return confirm('Delete this author? This cannot be undone.')">
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
                                        <i class="fas fa-feather"></i>
                                    </div>
                                    <h4>No authors found</h4>
                                    <p>Get started by adding your first author.</p>
                                    <a href="{{ route('admin.authors.create') }}" class="admin-btn admin-btn-primary">
                                        <i class="fas fa-plus"></i> Add Author
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