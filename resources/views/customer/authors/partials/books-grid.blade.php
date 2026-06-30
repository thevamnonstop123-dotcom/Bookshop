{{-- Books Grid --}}
<div class="author-books-grid">
    @forelse($books as $book)
        <x-customer.book-card :book="$book" :wishlistedIds="$wishlistedIds" />
    @empty
        <div class="author-books-empty">
            <i class="fas fa-book-sparkles"></i>
            <p>No books found for this author yet.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($books->hasPages())
    <div class="author-books-pagination">
        {{ $books->appends(request()->query())->links('vendor.pagination.default') }}
    </div>
@endif
