@extends('layouts.admin')

@section('title', 'Edit Book - Bookshop Admin')
@section('page_title', 'Edit Book')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <div class="form-container" style="max-width: 700px; background: var(--color-white); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-group">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $book->title) }}" required>
                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="category_id" class="form-label">Category</label>
                    <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" id="isbn" name="isbn" class="form-control @error('isbn') is-invalid @enderror"
                           value="{{ old('isbn', $book->isbn) }}" required>
                    @error('isbn') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Authors Drag & Drop --}}
            <div class="form-group">
                <label class="form-label">Authors</label>
                <div class="author-selection">
                    <div class="selected-authors-zone" id="selectedAuthorsZone"
                         ondragover="event.preventDefault(); this.classList.add('drop-active')"
                         ondragleave="this.classList.remove('drop-active')"
                         ondrop="handleAuthorDrop(event)">
                        <span class="drop-placeholder" id="noAuthorsMsg">Drop authors here or click to select</span>
                    </div>
                    <div class="available-authors-grid mt-3">
                        @foreach ($authors as $author)
                            <div class="author-chip {{ in_array($author->id, old('author_ids', $selectedAuthors)) ? 'selected' : '' }}"
                                 draggable="true"
                                 data-author-id="{{ $author->id }}"
                                 data-author-name="{{ $author->name }}"
                                 ondragstart="handleAuthorDragStart(event)"
                                 ondragend="handleAuthorDragEnd(event)"
                                 onclick="toggleAuthorSelect(this, {{ $author->id }})">
                                {{ $author->name }}
                            </div>
                        @endforeach
                    </div>
                    <div id="authorInputs"></div>
                    @error('author_ids') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="price" class="form-label">Price (MMK)</label>
                    <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', $book->price) }}" step="0.01" min="0" required>
                    @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="sale_price" class="form-label">Sale Price (MMK) — Optional</label>
                    <input type="number" id="sale_price" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror"
                           value="{{ old('sale_price', $book->sale_price) }}" step="0.01" min="0" placeholder="Leave empty for no discount">
                    @error('sale_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="sale_starts_at" class="form-label">Sale Starts</label>
                    <input type="datetime-local" id="sale_starts_at" name="sale_starts_at" class="form-control"
                           value="{{ old('sale_starts_at', $book->sale_starts_at ? $book->sale_starts_at->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="sale_ends_at" class="form-label">Sale Ends</label>
                    <input type="datetime-local" id="sale_ends_at" name="sale_ends_at" class="form-control"
                           value="{{ old('sale_ends_at', $book->sale_ends_at ? $book->sale_ends_at->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="stock_quantity" class="form-label">Stock Quantity</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror"
                           value="{{ old('stock_quantity', $book->stock_quantity) }}" min="0" required>
                    @error('stock_quantity') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" {{ $book->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $book->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="language" class="form-label">Language</label>
                    <input type="text" id="language" name="language" class="form-control @error('language') is-invalid @enderror"
                           value="{{ old('language', $book->language) }}" required>
                    @error('language') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="published_date" class="form-label">Published Date</label>
                    <input type="date" id="published_date" name="published_date" class="form-control @error('published_date') is-invalid @enderror"
                           value="{{ old('published_date', $book->published_date->format('Y-m-d')) }}" required>
                    @error('published_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="5">{{ old('description', $book->description) }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Current Cover</label>
                <div style="margin-bottom: 10px;">
                    <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/' . $book->image) : 'https://placehold.co/120x168/e2e8f0/64748b?text=Book' }}"
                         alt="{{ $book->title }}" style="width: 100px; border-radius: 6px;">
                </div>
                <label for="image" class="form-label">Change Cover (optional)</label>
                <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                <small style="color: var(--color-text-muted); font-size: 12px;">Max 2MB. Leave blank to keep current.</small>
                @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                <div style="margin-top: 10px;">
                    <img id="imagePreview" src="#" alt="Preview" style="display: none; width: 100px; border-radius: 6px;">
                </div>
            </div>

            <div class="d-flex gap-10 mt-20">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Book</button>
                <a href="{{ route('admin.books.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const img = document.getElementById('imagePreview');
        img.src = URL.createObjectURL(event.target.files[0]);
        img.style.display = 'block';
    }

    let selectedAuthors = @json(old('author_ids', $selectedAuthors));

    function renderSelectedAuthors() {
        const zone = document.getElementById('selectedAuthorsZone');
        const msg = document.getElementById('noAuthorsMsg');
        const inputs = document.getElementById('authorInputs');
        zone.querySelectorAll('.selected-author-chip').forEach(el => el.remove());
        inputs.innerHTML = '';
        if (selectedAuthors.length === 0) {
            msg.style.display = 'block';
        } else {
            msg.style.display = 'none';
            selectedAuthors.forEach(id => {
                const chip = document.querySelector('.author-chip[data-author-id="'+id+'"]');
                const name = chip ? chip.dataset.authorName : 'Author';
                const tag = document.createElement('span');
                tag.className = 'selected-author-chip';
                tag.innerHTML = name + ' <span class="remove-author" onclick="removeAuthor('+id+')">&times;</span>';
                zone.appendChild(tag);
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'author_ids[]'; input.value = id;
                inputs.appendChild(input);
            });
        }
        document.querySelectorAll('.author-chip').forEach(chip => {
            chip.classList.toggle('selected', selectedAuthors.includes(parseInt(chip.dataset.authorId)));
        });
    }

    function addAuthor(id) { if (!selectedAuthors.includes(id)) { selectedAuthors.push(id); renderSelectedAuthors(); } }
    function removeAuthor(id) { selectedAuthors = selectedAuthors.filter(a => a !== id); renderSelectedAuthors(); }
    function toggleAuthorSelect(chip, id) { selectedAuthors.includes(id) ? removeAuthor(id) : addAuthor(id); }

    let draggedId = null;
    function handleAuthorDragStart(e) { draggedId = parseInt(e.target.dataset.authorId); e.target.classList.add('dragging'); }
    function handleAuthorDragEnd(e) { e.target.classList.remove('dragging'); document.getElementById('selectedAuthorsZone').classList.remove('drop-active'); draggedId = null; }
    function handleAuthorDrop(e) { e.preventDefault(); document.getElementById('selectedAuthorsZone').classList.remove('drop-active'); if (draggedId) addAuthor(draggedId); }

    renderSelectedAuthors();
</script>
@endpush