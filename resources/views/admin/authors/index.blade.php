@extends('layouts.admin')

@section('title', 'Authors - Bookshop Admin')
@section('page_title', 'Author Management')

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
            <h2>All Authors</h2>
            <a href="{{ route('admin.authors.create') }}" class="btn btn-accent btn-sm">
                <i class="fas fa-plus"></i> Add Author
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Bio</th>
                    <th>Books</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($authors as $author)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <img src="{{ $author->image ? asset('storage/' . $author->image) : 'https://ui-avatars.com/api/?name=' . urlencode($author->name) . '&background=f59e0b&color=fff&size=40' }}"
                                 alt="{{ $author->name }}"
                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                        </td>
                        <td class="font-semibold">{{ $author->name }}</td>
                        <td>{{ Str::limit($author->bio, 60) ?: '—' }}</td>
                        <td>{{ $author->books_count }}</td>
                        <td>
                            <span class="badge {{ $author->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($author->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-5">
                                <a href="{{ route('admin.authors.edit', $author) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.authors.destroy', $author) }}" method="POST"
                                      onsubmit="return confirm('Delete this author?')">
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
                            <i class="fas fa-feather-alt" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                            No authors found. <a href="{{ route('admin.authors.create') }}">Add your first author</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection