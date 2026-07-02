@extends('layouts.admin')

@section('title', 'Books — Admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')
<div class="admin-table-card">

    {{-- Header + Filter Bar — Sticky Group --}}
    <div class="admin-table-toolbar">
        <div class="admin-table-header">
            <div class="admin-table-header-left">
                <h2 class="admin-table-title">All Books</h2>
                <span class="admin-table-count">{{ $books->total() }} {{ Str::plural('book', $books->total()) }}</span>
            </div>
            <a href="{{ route('admin.books.create') }}" class="admin-btn admin-btn-primary">
                <i class="fas fa-plus"></i> Add Book
            </a>
        </div>

        <div class="admin-filter-bar">
            <form action="{{ route('admin.books.index') }}" method="GET" class="admin-filter-form">
                <select name="availability" class="admin-form-input admin-form-select" onchange="this.form.submit()">
                    <option value="">All Availability</option>
                    <option value="in_stock" {{ request('availability') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('availability') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('availability') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="coming_soon" {{ request('availability') === 'coming_soon' ? 'selected' : '' }}>Coming Soon</option>
                    <option value="pre_order" {{ request('availability') === 'pre_order' ? 'selected' : '' }}>Pre-order</option>
                    <option value="discontinued" {{ request('availability') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                </select>
                @if(request('availability'))
                    <a href="{{ route('admin.books.index') }}" class="admin-btn admin-btn-outline admin-btn-sm">Clear</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:40px;"><input type="checkbox" id="selectAll" onclick="toggleAllBooks(this)"></th>
                    <th style="width:70px;">Cover</th>
                    <th>Title</th>
                    <th style="width:130px;">Category</th>
                    <th style="width:160px;">Authors</th>
                    <th style="width:110px;">Price</th>
                    <th style="width:130px;">Availability</th>
                    <th style="width:90px;">Status</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr>
                        <td><input type="checkbox" name="book_ids[]" value="{{ $book->id }}" class="book-checkbox" onchange="updateBulkBar()"></td>
                        <td>
                            <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/' . $book->image) : 'https://placehold.co/60x80/F1F5F9/94A3B8?text=Book' }}"
                                 alt="{{ $book->title }}" class="admin-table-book-cover" loading="lazy">
                        </td>
                        <td>
                            <div class="admin-table-name">{{ $book->title }}</div>
                            @if($book->isEbook())
                                <span class="admin-table-ebook-tag"><i class="fas fa-bolt"></i> E-Book</span>
                            @endif
                        </td>
                        <td><span class="admin-table-category">{{ $book->category?->name ?? '—' }}</span></td>
                        <td class="admin-table-authors">{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</td>
                        <td>
                            @if($book->isOnSale())
                                <span class="admin-table-price-sale">{{ number_format($book->sale_price) }} MMK</span>
                            @else
                                <span>{{ number_format($book->price) }} MMK</span>
                            @endif
                        </td>
                        <td>
                            @php $avail = $book->availability; @endphp
                            <span class="admin-badge" style="background:{{ $avail['bg'] }};color:{{ $avail['text'] }};">
                                <i class="fas {{ $avail['icon'] }}"></i> {{ $avail['label'] }}
                            </span>
                        </td>
                        <td>
                            <span class="admin-badge admin-badge-{{ $book->status === 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($book->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a href="{{ route('admin.books.edit', $book) }}" class="admin-action-btn admin-action-edit" title="Edit">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
                                      onsubmit="return confirm('Delete this book?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="admin-action-btn admin-action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:var(--color-text-muted);">
                            No books found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($books->hasPages())
        <div class="admin-pagination">
            {{ $books->appends(request()->query())->links('vendor.pagination.default') }}
        </div>
    @endif
</div>

{{-- Floating Bulk Bar — Bottom of screen --}}
<div class="bulk-bar" id="bulkBar">
    <span class="bulk-bar-count" id="bulkCount">0</span> books selected
    <form action="{{ route('admin.books.bulk-update') }}" method="POST" class="bulk-bar-form">
        @csrf
        <input type="hidden" name="book_ids" id="bulkBookIds" value="">
        <select name="availability_status" class="admin-form-input admin-form-select">
            <option value="">Change to...</option>
            <option value="in_stock">In Stock</option>
            <option value="low_stock">Low Stock</option>
            <option value="out_of_stock">Out of Stock</option>
            <option value="coming_soon">Coming Soon</option>
            <option value="pre_order">Pre-order</option>
            <option value="discontinued">Discontinued</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">Apply</button>
    </form>
</div>

<script>
function toggleAllBooks(el) {
    document.querySelectorAll('.book-checkbox').forEach(cb => cb.checked = el.checked);
    updateBulkBar();
}
function updateBulkBar() {
    const checked = document.querySelectorAll('.book-checkbox:checked');
    const ids = Array.from(checked).map(cb => cb.value);
    document.getElementById('bulkBookIds').value = ids.join(',');
    document.getElementById('bulkCount').textContent = checked.length;
    document.getElementById('bulkBar').classList.toggle('show', checked.length > 0);
}
</script>
@endsection
