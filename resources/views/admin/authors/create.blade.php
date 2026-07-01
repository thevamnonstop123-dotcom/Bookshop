@extends('layouts.admin')

@section('title', 'Add Author — Bookshop Admin')
@section('page_title', 'Add New Author')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    {{-- Back Link --}}
    <a href="{{ route('admin.authors.index') }}" class="admin-form-back">
        <i class="fas fa-arrow-left"></i> Back to Authors
    </a>

    <div class="admin-form-card">
        <div class="admin-form-card-header">
            <div class="admin-form-card-icon">
                <i class="fas fa-feather"></i>
            </div>
            <div>
                <h2 class="admin-form-card-title">Author Information</h2>
                <p class="admin-form-card-subtitle">Fill in the details to create a new author profile</p>
            </div>
        </div>

        <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf

            <div class="admin-form-grid">
                {{-- Name --}}
                <div class="admin-form-group">
                    <label for="name" class="admin-form-label">
                        Author Name <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-user-pen admin-form-input-icon"></i>
                        <input type="text" id="name" name="name"
                               class="admin-form-input @error('name') admin-form-input-error @enderror"
                               value="{{ old('name') }}" placeholder="e.g., James Clear" required autofocus>
                    </div>
                    @error('name')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Country --}}
                <div class="admin-form-group">
                    <label class="admin-form-label">Country</label>
                    <select name="country_id" class="admin-form-input admin-form-select">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $author->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Genres --}}
                <div class="admin-form-group">
                    <label class="admin-form-label">Genres</label>
                    <div class="admin-multi-select" id="genreSelect">
                        <button type="button" class="admin-multi-select-trigger" onclick="document.getElementById('genreDropdown').classList.toggle('open')">
                            <span id="genreSelectedText">Select genres</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="admin-multi-select-dropdown" id="genreDropdown">
                            @foreach($genres as $genre)
                                <label class="admin-multi-select-option">
                                    <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                                        {{ isset($author) && $author->genres->contains($genre->id) ? 'checked' : '' }}
                                        onchange="updateGenreSelect()">
                                    <span class="admin-multi-select-check"></span>
                                    {{ $genre->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Website --}}
                <div class="admin-form-group">
                    <label class="admin-form-label">Website</label>
                    <input type="url" name="website" class="admin-form-input" 
                        value="{{ old('website', $author->website ?? '') }}" placeholder="https://">
                </div>

                {{-- Joined Date --}}
                <div class="admin-form-group">
                    <label class="admin-form-label">Joined Date</label>
                    <input type="date" name="joined_date" class="admin-form-input" 
                        value="{{ old('joined_date', isset($author) ? $author->joined_date?->format('Y-m-d') : '') }}">
                </div>

                {{-- Status --}}
                <div class="admin-form-group">
                    <label for="status" class="admin-form-label">Status</label>
                    <select id="status" name="status" class="admin-form-input admin-form-select @error('status') admin-form-input-error @enderror">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Biography --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="bio" class="admin-form-label">Biography</label>
                    <textarea id="bio" name="bio"
                              class="admin-form-input admin-form-textarea @error('bio') admin-form-input-error @enderror"
                              rows="5" placeholder="Brief biography of the author">{{ old('bio') }}</textarea>
                    @error('bio')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Photo --}}
                <div class="admin-form-group admin-form-group-full">
                    <label class="admin-form-label">
                        Author Photo <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-image-upload">
                        <img id="imagePreview" src="#" alt="Preview"
                             class="admin-form-image-preview" style="display: none;">
                        <div class="admin-form-image-placeholder" id="imagePlaceholder">
                            <i class="fas fa-camera"></i>
                            <span>No photo selected</span>
                        </div>
                        <label for="image" class="admin-form-image-btn">
                            <i class="fas fa-upload"></i> Choose Photo
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

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-check"></i> Save Author
                </button>
                <a href="{{ route('admin.authors.index') }}" class="admin-btn admin-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/form.js') }}"></script>
@endpush