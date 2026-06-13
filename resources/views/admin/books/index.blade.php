@extends('layouts.admin')

@section('title', 'Books — Bookshop Admin')
@section('page_title', 'Book Management')

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
                <h2 class="admin-table-title">All Books</h2>
                <span class="admin-table-count">{{ $books->total() }} {{ Str::plural('book', $books->total()) }}</span>
            </div>
            <a href="{{ route('admin.books.create') }}" class="admin-btn admin-btn-primary">
                <i class="fas fa-plus"></i> Add Book
            </a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Cover</th>
                        <th>Title</th>
                        <th style="width: 130px;">Category</th>
                        <th style="width: 160px;">Authors</th>
                        <th style="width: 110px;">Price</th>
                        <th style="width: 80px;">Stock</th>
                        <th style="width: 90px;">Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($books as $book)
                        <tr>
                            <td>
                                <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/' . $book->image) : 'https://placehold.co/60x80/F1F5F9/94A3B8?text=Book' }}"
                                     alt="{{ $book->title }}"
                                     class="admin-table-book-cover"
                                     loading="lazy">
                            </td>
                            <td>
                                <div class="admin-table-name">{{ $book->title }}</div>
                                @if($book->isEbook())
                                    <span class="admin-table-ebook-tag">
                                        <i class="fas fa-bolt"></i> E-Book
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="admin-table-category">{{ $book->category?->name ?? '—' }}</span>
                            </td>
                            <td class="admin-table-authors">
                                {{ $book->authors->pluck('name')->join(', ') ?: '—' }}
                            </td>
                            <td>
                                <div class="admin-table-price">
                                    @if($book->isOnSale())
                                        <span class="admin-table-price-original">{{ number_format($book->price) }}</span>
                                        <span class="admin-table-price-sale">{{ number_format($book->sale_price) }} MMK</span>
                                    @else
                                        <span class="admin-table-price-regular">{{ number_format($book->price) }} MMK</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $book->stock_quantity > 5 ? 'success' : ($book->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                    {{ $book->isEbook() ? '∞' : $book->stock_quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $book->status === 'active' ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $book->status === 'active' ? 'circle-check' : 'circle-xmark' }}"></i>
                                    {{ ucfirst($book->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="{{ route('admin.books.edit', $book) }}" class="admin-action-btn admin-action-edit" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
                                          onsubmit="return confirm('Delete this book? This cannot be undone.')">
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
                            <td colspan="8">
                                <div class="admin-table-empty">
                                    <div class="admin-table-empty-icon">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <h4>No books found</h4>
                                    <p>Get started by adding your first book to the catalog.</p>
                                    <a href="{{ route('admin.books.create') }}" class="admin-btn admin-btn-primary">
                                        <i class="fas fa-plus"></i> Add Book
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($books->hasPages())
            <div class="admin-table-pagination">
                {{ $books->links('vendor.pagination.default') }}
            </div>
        @endif
    </div>

@endsection