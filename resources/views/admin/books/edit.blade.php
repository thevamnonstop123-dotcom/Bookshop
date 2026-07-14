@extends('layouts.admin')

@section('title', 'Edit Book — Bookshop Admin')
@section('page_title', 'Edit Book')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/drag-drop.css') }}">
@endpush

@section('content')

    <a href="{{ route('admin.books.index') }}" class="admin-form-back">
        <i class="fas fa-arrow-left"></i> Back to Books
    </a>

    <div class="admin-form-card">
        <div class="admin-form-card-header">
            <div class="admin-form-card-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div>
                <h2 class="admin-form-card-title">Edit Book</h2>
                <p class="admin-form-card-subtitle">Update information for <strong>{{ $book->title }}</strong></p>
            </div>
        </div>

        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf @method('PUT')

            <div class="admin-form-grid">
                {{-- Title --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="title" class="admin-form-label">
                        Book Title <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-heading admin-form-input-icon"></i>
                        <input type="text" id="title" name="title"
                               class="admin-form-input @error('title') admin-form-input-error @enderror"
                               value="{{ old('title', $book->title) }}" required>
                    </div>
                    @error('title')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="admin-form-group">
                    <label for="category_id" class="admin-form-label">
                        Category <span class="admin-form-required">*</span>
                    </label>
                    <select id="category_id" name="category_id"
                            class="admin-form-input admin-form-select @error('category_id') admin-form-input-error @enderror" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ISBN --}}
                <div class="admin-form-group">
                    <label for="isbn" class="admin-form-label">
                        ISBN <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-barcode admin-form-input-icon"></i>
                        <input type="text" id="isbn" name="isbn"
                               class="admin-form-input @error('isbn') admin-form-input-error @enderror"
                               value="{{ old('isbn', $book->isbn) }}" required>
                    </div>
                    @error('isbn')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Authors --}}
                <div class="admin-form-group admin-form-group-full">
                    <label class="admin-form-label">Authors</label>

                    <div class="author-selection-container">
                        <div class="author-dropzone" id="selectedAuthorsZone"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)"
                             ondrop="handleAuthorDrop(event)">
                            <span class="author-dropzone-placeholder" id="noAuthorsMsg">
                                <i class="fas fa-hand-pointer"></i> Drop authors here or click below to select
                            </span>
                        </div>

                        <div class="author-grid-label">Available Authors</div>
                        <div class="author-grid" id="authorGrid">
                            @foreach ($authors as $author)
                                <button type="button"
                                        class="author-chip {{ in_array($author->id, old('author_ids', $selectedAuthors)) ? 'author-chip-selected' : '' }}"
                                        draggable="true"
                                        data-author-id="{{ $author->id }}"
                                        data-author-name="{{ $author->name }}"
                                        ondragstart="handleAuthorDragStart(event)"
                                        ondragend="handleAuthorDragEnd(event)"
                                        onclick="toggleAuthorSelect(this, {{ $author->id }})">
                                    <i class="fas fa-feather"></i>
                                    {{ $author->name }}
                                </button>
                            @endforeach
                        </div>

                        <div id="authorInputs"></div>
                    </div>

                    @error('author_ids')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Price --}}
                <div class="admin-form-group">
                    <label for="price" class="admin-form-label">
                        Price (MMK) <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-coins admin-form-input-icon"></i>
                        <input type="number" id="price" name="price"
                               class="admin-form-input @error('price') admin-form-input-error @enderror"
                               value="{{ old('price', $book->price) }}" step="0.01" min="0" required>
                    </div>
                    @error('price')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Sale Price --}}
                <div class="admin-form-group">
                    <label for="sale_price" class="admin-form-label">Sale Price (MMK)</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-tag admin-form-input-icon"></i>
                        <input type="number" id="sale_price" name="sale_price"
                               class="admin-form-input @error('sale_price') admin-form-input-error @enderror"
                               value="{{ old('sale_price', $book->sale_price) }}" step="0.01" min="0"
                               placeholder="Leave empty for no discount">
                    </div>
                    <span class="admin-form-hint">Optional — leave blank for regular price</span>
                    @error('sale_price')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Stock --}}
                <div class="admin-form-group">
                    <label for="stock_quantity" class="admin-form-label">Stock Quantity</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-boxes admin-form-input-icon"></i>
                        <input type="number" id="stock_quantity" name="stock_quantity"
                               class="admin-form-input @error('stock_quantity') admin-form-input-error @enderror"
                               value="{{ old('stock_quantity', $book->stock_quantity) }}" min="0">
                    </div>
                    <span class="admin-form-hint">
                        @if($book->isEbook())
                            E-book — unlimited access
                        @else
                            Current stock: {{ $book->stock_quantity }} units
                        @endif
                    </span>
                    @error('stock_quantity')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label for="availability_status" class="admin-form-label">Availability Status</label>
                    <select id="availability_status" name="availability_status" class="admin-form-input admin-form-select">
                        @foreach($availabilityOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('availability_status', 'in_stock') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="admin-form-group">
                    <label for="status" class="admin-form-label">Status</label>
                    <select id="status" name="status" class="admin-form-input admin-form-select">
                        <option value="active" {{ $book->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $book->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Language --}}
                <div class="admin-form-group">
                    <label for="language" class="admin-form-label">Language</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-language admin-form-input-icon"></i>
                        <input type="text" id="language" name="language"
                               class="admin-form-input @error('language') admin-form-input-error @enderror"
                               value="{{ old('language', $book->language) }}" required>
                    </div>
                    @error('language')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Published Date --}}
                <div class="admin-form-group">
                    <label for="published_date" class="admin-form-label">
                        Published Date <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-calendar admin-form-input-icon"></i>
                        <input type="date" id="published_date" name="published_date"
                               class="admin-form-input @error('published_date') admin-form-input-error @enderror"
                               value="{{ old('published_date', $book->published_date->format('Y-m-d')) }}" required>
                    </div>
                    @error('published_date')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Sale Period --}}
                <div class="admin-form-group">
                    <label for="sale_starts_at" class="admin-form-label">Sale Starts</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-calendar-plus admin-form-input-icon"></i>
                        <input type="datetime-local" id="sale_starts_at" name="sale_starts_at"
                               class="admin-form-input"
                               value="{{ old('sale_starts_at', $book->sale_starts_at ? $book->sale_starts_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label for="sale_ends_at" class="admin-form-label">Sale Ends</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-calendar-xmark admin-form-input-icon"></i>
                        <input type="datetime-local" id="sale_ends_at" name="sale_ends_at"
                               class="admin-form-input"
                               value="{{ old('sale_ends_at', $book->sale_ends_at ? $book->sale_ends_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>

                {{-- Book Type Toggle --}}
                <div class="admin-form-group admin-form-group-full">
                    <label class="admin-form-label">Book Format</label>
                    <label class="admin-form-toggle">
                        <input type="checkbox" name="is_ebook" value="1"
                               {{ old('is_ebook') ? 'checked' : '' }}
                               onchange="toggleEbookFields(this)" id="isEbookCheckbox">
                        <span class="admin-form-toggle-switch"></span>
                        <span class="admin-form-toggle-label">
                            <i class="fas fa-tablet-screen-button"></i> This is an E-Book
                        </span>
                    </label>
                </div>

                {{-- E-Book PDF Upload --}}
                <div class="admin-form-group admin-form-group-full" id="ebookFields" style="display: {{ old('is_ebook') ? 'block' : 'none' }};">
                    <label for="ebook_file" class="admin-form-label">E-Book PDF File</label>
                    <div class="admin-form-file-upload">
                        <i class="fas fa-file-pdf admin-form-file-icon"></i>
                        <div class="admin-form-file-info">
                            <span class="admin-form-file-label">Upload PDF</span>
                            <span class="admin-form-file-hint">Max 20MB</span>
                        </div>
                        <input type="file" id="ebook_file" name="ebook_file"
                               class="admin-form-input-file @error('ebook_file') admin-form-input-error @enderror" accept=".pdf">
                        <label for="ebook_file" class="admin-form-file-btn">
                            <i class="fas fa-upload"></i> Choose File
                        </label>
                    </div>
                    @error('ebook_file')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror

                    <label for="ebook_price" class="admin-form-label" style="margin-top:16px;">E-Book Price (MMK)</label>
                    <input type="number" id="ebook_price" name="ebook_price"
                           class="admin-form-input"
                           value="{{ old('ebook_price', $book->ebook_price) }}" placeholder="e.g. 9999" step="0.01" min="0">
                    <span class="admin-form-hint">Usually cheaper than physical book</span>
                </div>
                    <label for="description" class="admin-form-label">Description</label>
                    <textarea id="description" name="description"
                              class="admin-form-input admin-form-textarea @error('description') admin-form-input-error @enderror"
                              rows="5" placeholder="Book description">{{ old('description', $book->description) }}</textarea>
                    @error('description')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Cover --}}
                <div class="admin-form-group admin-form-group-full">
                    <label class="admin-form-label">Book Cover</label>
                    <div class="admin-form-current-image">
                        <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/' . $book->image) : 'https://placehold.co/100x130/F1F5F9/94A3B8?text=Book' }}"
                             alt="{{ $book->title }}"
                             class="admin-form-current-img-book">
                        <div>
                            <span class="admin-form-current-label">Current cover</span>
                            <p class="admin-form-current-name">{{ $book->title }}</p>
                        </div>
                    </div>
                    <div class="admin-form-image-upload">
                        <img id="imagePreview" src="#" alt="New cover preview"
                             class="admin-form-image-preview admin-form-image-preview-book" style="display: none;">
                        <div class="admin-form-image-placeholder admin-form-image-placeholder-book" id="imagePlaceholder">
                            <i class="fas fa-book"></i>
                            <span>New cover preview</span>
                        </div>
                        <label for="image" class="admin-form-image-btn">
                            <i class="fas fa-upload"></i> Change Cover
                        </label>
                        <input type="file" id="image" name="image"
                               class="admin-form-input-file @error('image') admin-form-input-error @enderror"
                               accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                        <span class="admin-form-image-hint">Leave blank to keep current. JPG, JPEG, PNG. Max 2MB.</span>
                    </div>
                    @error('image')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-check"></i> Update Book
                </button>
                <a href="{{ route('admin.books.index') }}" class="admin-btn admin-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        window.bookCreateRoutes = {
            aiDescription: '{{ route("admin.ai.generate-description") }}',
            aiBulkCreate: '{{ route("admin.ai.bulk-create") }}',
            booksIndex: '{{ route("admin.books.index") }}',
        };
        window.selectedAuthors = @json(old('author_ids', $selectedAuthors));
    </script>
    <script src="{{ asset('js/admin/form.js') }}"></script>
    <script src="{{ asset('js/admin/drag-drop-authors.js') }}"></script>
    <script src="{{ asset('js/admin/book-create.js') }}"></script>
@endpush