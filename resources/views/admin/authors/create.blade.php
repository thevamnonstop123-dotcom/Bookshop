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