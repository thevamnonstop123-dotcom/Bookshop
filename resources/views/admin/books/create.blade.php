@extends('layouts.admin')

@section('title', 'Add Book — Bookshop Admin')
@section('page_title', 'Add New Book')

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
                <h2 class="admin-form-card-title">Book Information</h2>
                <p class="admin-form-card-subtitle">Fill in the details to add a new book to your catalog</p>
            </div>
        </div>

        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="admin-form" id="bookCreateForm">
            @csrf

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
                               value="{{ old('title') }}" placeholder="e.g., Atomic Habits" required autofocus>
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
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                               value="{{ old('isbn') }}" placeholder="e.g., 978-0735211292" required>
                    </div>
                    @error('isbn')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Authors — Drag & Drop --}}
                <div class="admin-form-group admin-form-group-full">
                    <label class="admin-form-label">
                        Authors <span class="admin-form-required">*</span>
                    </label>

                    <div class="author-selection-container">
                        {{-- Selected Authors Zone --}}
                        <div class="author-dropzone" id="selectedAuthorsZone"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)"
                             ondrop="handleAuthorDrop(event)">
                            <span class="author-dropzone-placeholder" id="noAuthorsMsg">
                                <i class="fas fa-hand-pointer"></i> Drop authors here or click below to select
                            </span>
                        </div>

                        {{-- Available Authors Grid --}}
                        <div class="author-grid-label">Available Authors</div>
                        <div class="author-grid" id="authorGrid">
                            @foreach ($authors as $author)
                                <button type="button"
                                        class="author-chip {{ in_array($author->id, old('author_ids', [])) ? 'author-chip-selected' : '' }}"
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

                        {{-- Hidden Inputs for Selected Authors --}}
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
                               value="{{ old('price') }}" placeholder="0.00" step="0.01" min="0" required>
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
                               class="admin-form-input"
                               value="{{ old('sale_price') }}" placeholder="Leave empty for no discount" step="0.01" min="0">
                    </div>
                    <span class="admin-form-hint">Optional — leave blank for regular price</span>
                </div>

                {{-- Stock Quantity --}}
                <div class="admin-form-group" id="stockField">
                    <label for="stock_quantity" class="admin-form-label">Stock Quantity</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-boxes admin-form-input-icon"></i>
                        <input type="number" id="stock_quantity" name="stock_quantity"
                               class="admin-form-input @error('stock_quantity') admin-form-input-error @enderror"
                               value="{{ old('stock_quantity', 0) }}" min="0">
                    </div>
                    <span class="admin-form-hint">Set to 0 for e-books</span>
                    @error('stock_quantity')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Availability Status --}}
                <div class="admin-form-group">
                    <label for="availability_status" class="admin-form-label">Availability Status</label>
                    <select id="availability_status" name="availability_status"
                            class="admin-form-input admin-form-select">
                        @foreach($availabilityOptions as $value => $label)
                            <option value="{{ $value }}" {{ old("availability_status", "in_stock") == $value ? "selected" : "" }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error("availability_status")
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="admin-form-group">
                    <label for="status" class="admin-form-label">Status</label>
                    <select id="status" name="status"
                            class="admin-form-input admin-form-select @error('status') admin-form-input-error @enderror">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Language --}}
                <div class="admin-form-group">
                    <label for="language" class="admin-form-label">Language</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-language admin-form-input-icon"></i>
                        <input type="text" id="language" name="language"
                               class="admin-form-input @error('language') admin-form-input-error @enderror"
                               value="{{ old('language', 'English') }}" placeholder="e.g., English" required>
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
                               value="{{ old('published_date') }}" required>
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
                               class="admin-form-input" value="{{ old('sale_starts_at') }}">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label for="sale_ends_at" class="admin-form-label">Sale Ends</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-calendar-xmark admin-form-input-icon"></i>
                        <input type="datetime-local" id="sale_ends_at" name="sale_ends_at"
                               class="admin-form-input" value="{{ old('sale_ends_at') }}">
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
                           value="{{ old('ebook_price') }}" placeholder="e.g. 9999" step="0.01" min="0">
                    <span class="admin-form-hint">Usually cheaper than physical book</span>
                </div>
                <div class="admin-form-group admin-form-group-full">
                    <label for="description" class="admin-form-label">Description</label>
                    <textarea id="description" name="description"
                              class="admin-form-input admin-form-textarea @error('description') admin-form-input-error @enderror"
                              rows="5" placeholder="Book description">{{ old('description') }}</textarea>
                    <div class="admin-form-description-actions">
                        <button type="button" class="admin-btn admin-btn-ghost admin-btn-sm" onclick="generateDescription()" id="generateDescBtn">
                            <i class="fas fa-wand-magic-sparkles"></i> Generate with AI
                        </button>
                        <span class="admin-form-loading" id="aiDescLoading">
                            <i class="fas fa-spinner fa-spin"></i> Generating...
                        </span>
                    </div>
                    @error('description')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Book Cover --}}
                <div class="admin-form-group admin-form-group-full" id="imageField">
                    <label class="admin-form-label">
                        Book Cover <span class="admin-form-required" id="imageRequired">*</span>
                    </label>
                    <div class="admin-form-image-upload">
                        <img id="imagePreview" src="#" alt="Cover preview"
                             class="admin-form-image-preview admin-form-image-preview-book" style="display: none;">
                        <div class="admin-form-image-placeholder admin-form-image-placeholder-book" id="imagePlaceholder">
                            <i class="fas fa-book"></i>
                            <span>No cover selected</span>
                        </div>
                        <label for="image" class="admin-form-image-btn">
                            <i class="fas fa-upload"></i> Choose Cover
                        </label>
                        <input type="file" id="image" name="image"
                               class="admin-form-input-file @error('image') admin-form-input-error @enderror"
                               accept=".jpg,.jpeg,.png" required onchange="previewImage(event)">
                        <span class="admin-form-image-hint">JPG, JPEG or PNG. Max 2MB.</span>
                    </div>
                    @error('image')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="admin-form-actions">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-check"></i> Save Book
                </button>
                <a href="{{ route('admin.books.index') }}" class="admin-btn admin-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

    {{-- AI Bulk Create Section --}}
    <div class="admin-form-card" style="margin-top: 28px;">
        <div class="admin-form-card-header">
            <div class="admin-form-card-icon" style="background: #EFF6FF; color: #2563EB;">
                <i class="fas fa-wand-magic-sparkles"></i>
            </div>
            <div>
                <h2 class="admin-form-card-title">AI Quick Create</h2>
                <p class="admin-form-card-subtitle">Generate multiple books at once using AI</p>
            </div>
        </div>

        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label for="aiCategory" class="admin-form-label">Category</label>
                <select id="aiCategory" class="admin-form-input admin-form-select">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-form-group">
                <label for="aiLanguage" class="admin-form-label">Language</label>
                <div class="admin-form-input-wrapper">
                    <i class="fas fa-language admin-form-input-icon"></i>
                    <input type="text" id="aiLanguage" class="admin-form-input" value="English">
                </div>
            </div>
            <div class="admin-form-group">
                <label for="aiCount" class="admin-form-label">Number of Books</label>
                <div class="admin-form-input-wrapper">
                    <i class="fas fa-hashtag admin-form-input-icon"></i>
                    <input type="number" id="aiCount" class="admin-form-input" value="5" min="1" max="10">
                </div>
            </div>
            <div class="admin-form-group">
                <label for="aiTopic" class="admin-form-label">Topic (optional)</label>
                <div class="admin-form-input-wrapper">
                    <i class="fas fa-tag admin-form-input-icon"></i>
                    <input type="text" id="aiTopic" class="admin-form-input" placeholder="e.g., Python, JavaScript">
                </div>
            </div>
            <div class="admin-form-group">
                <label for="aiStock" class="admin-form-label">Stock per Book</label>
                <div class="admin-form-input-wrapper">
                    <i class="fas fa-boxes admin-form-input-icon"></i>
                    <input type="number" id="aiStock" class="admin-form-input" value="50" min="0">
                </div>
            </div>
            <div class="admin-form-group admin-form-group-full">
                <div class="ai-bulk-actions">
                    <button type="button" class="admin-btn admin-btn-primary" onclick="bulkCreateBooks()" id="bulkCreateBtn">
                        <i class="fas fa-wand-magic-sparkles"></i> Generate Books with AI
                    </button>
                    <span class="admin-form-loading" id="aiBulkLoading">
                        <i class="fas fa-spinner fa-spin"></i> AI is creating books...
                    </span>
                </div>
                <div id="aiBulkResult" style="margin-top: 12px;"></div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.bookCreateRoutes = {
            aiDescription: '{{ route("admin.ai.generate-description") }}',
            aiBulkCreate: '{{ route("admin.ai.bulk-create") }}',
            booksIndex: '{{ route("admin.books.index") }}',
        };
        window.selectedAuthors = @json(old('author_ids', []));
    </script>
    <script src="{{ asset('js/admin/form.js') }}"></script>
    <script src="{{ asset('js/admin/drag-drop-authors.js') }}"></script>
    <script src="{{ asset('js/admin/book-create.js') }}"></script>
@endpush