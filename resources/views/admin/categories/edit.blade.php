@extends('layouts.admin')

@section('title', 'Edit Category — Bookshop Admin')
@section('page_title', 'Edit Category')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <a href="{{ route('admin.categories.index') }}" class="admin-form-back">
        <i class="fas fa-arrow-left"></i> Back to Categories
    </a>

    <div class="admin-form-card">
        <div class="admin-form-card-header">
            <div class="admin-form-card-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h2 class="admin-form-card-title">Edit Category</h2>
                <p class="admin-form-card-subtitle">Update information for <strong>{{ $category->name }}</strong></p>
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="admin-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="admin-form-grid">
                {{-- Name --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="name" class="admin-form-label">
                        Category Name <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-tag admin-form-input-icon"></i>
                        <input type="text" id="name" name="name"
                               class="admin-form-input @error('name') admin-form-input-error @enderror"
                               value="{{ old('name', $category->name) }}" required>
                    </div>
                    @error('name')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="description" class="admin-form-label">Description</label>
                    <textarea id="description" name="description"
                              class="admin-form-input admin-form-textarea @error('description') admin-form-input-error @enderror"
                              rows="3">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="admin-form-group">
                    <label for="status" class="admin-form-label">Status</label>
                    <select id="status" name="status" class="admin-form-input admin-form-select">
                        <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            {{-- Image --}}
            <div class="admin-form-group admin-form-group-full">
                <label class="admin-form-label">Category Image</label>
                <div class="admin-form-image-upload">
                    @if($category->image && $category->image !== 'default.png')
                        <img src="{{ asset('storage/'.$category->image) }}" alt="Current image" class="admin-form-image-preview-banner" id="imagePreview">
                        <div class="admin-form-image-placeholder-banner" id="imagePlaceholder" style="display:none;">
                            <i class="fas fa-image"></i> No image
                        </div>
                    @else
                        <div class="admin-form-image-placeholder-banner" id="imagePlaceholder">
                            <i class="fas fa-image"></i> No image
                        </div>
                        <img id="imagePreview" style="display:none;" class="admin-form-image-preview-banner">
                    @endif
                    <div>
                        <label class="admin-form-image-btn">
                            <i class="fas fa-upload"></i> Choose Image
                            <input type="file" name="image" class="admin-form-input-file" accept=".jpg,.jpeg,.png,.webp" onchange="previewImage(event)">
                        </label>
                        <span class="admin-form-image-hint">JPG, JPEG, PNG or WEBP. Max 2MB. Recommended: 400×300px</span>
                    </div>
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-check"></i> Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

@endsection