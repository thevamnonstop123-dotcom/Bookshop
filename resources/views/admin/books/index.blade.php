@extends('layouts.admin')

@section('title', 'Books - Bookshop Admin')
@section('page_title', 'Book Management')

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
            <h2>All Books</h2>
            <a href="{{ route('admin.books.create') }}" class="btn btn-accent btn-sm">
                <i class="fas fa-plus"></i> Add Book
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Authors</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr>
                        <td>
                            <img src="{{ $book->image ? asset('storage/' . $book->image) : 'https://placehold.co/60x80/e2e8f0/64748b?text=Book' }}"
                                 alt="{{ $book->title }}"
                                 style="width: 40px; height: 56px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td class="font-semibold">{{ $book->title }}</td>
                        <td>{{ $book->category?->name ?? '—' }}</td>
                        <td>{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</td>
                        <td>{{ number_format($book->price) }} MMK</td>
                        <td>
                            <span class="badge {{ $book->stock_quantity > 5 ? 'badge-success' : ($book->stock_quantity > 0 ? 'badge-warning' : 'badge-danger') }}">
                                {{ $book->stock_quantity }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $book->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($book->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-5">
                                <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
                                      onsubmit="return confirm('Delete this book?')">
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
                        <td colspan="8" class="text-center" style="padding: 40px; color: var(--color-text-muted);">
                            <i class="fas fa-book" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                            No books found. <a href="{{ route('admin.books.create') }}">Add your first book</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection