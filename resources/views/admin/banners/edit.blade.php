@extends('layouts.admin')

@section('title', 'Edit Banner — Bookshop Admin')
@section('page_title', 'Edit Banner')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <a href="{{ route('admin.banners.index') }}" class="admin-form-back">
        <i class="fas fa-arrow-left"></i> Back to Banners
    </a>

    <div class="admin-form-card">
        <div class="admin-form-card-header">
            <div class="admin-form-card-icon">
                <i class="fas fa-image"></i>
            </div>
            <div>
                <h2 class="admin-form-card-title">Edit Banner</h2>
                <p class="admin-form-card-subtitle">Update information for <strong>{{ $banner->title }}</strong></p>
            </div>
        </div>

        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf
            @method('PUT')

            <div class="admin-form-grid">
                {{-- Title --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="title" class="admin-form-label">
                        Title <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-heading admin-form-input-icon"></i>
                        <input type="text" id="title" name="title"
                               class="admin-form-input @error('title') admin-form-input-error @enderror"
                               value="{{ old('title', $banner->title) }}" required>
                    </div>
                    @error('title')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="description" class="admin-form-label">Description</label>
                    <textarea id="description" name="description"
                              class="admin-form-input admin-form-textarea @error('description') admin-form-input-error @enderror"
                              rows="3" placeholder="Brief description of the promotion">{{ old('description', $banner->description) }}</textarea>
                    @error('description')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Display Order --}}
                <div class="admin-form-group">
                    <label for="display_order" class="admin-form-label">Display Order</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-sort admin-form-input-icon"></i>
                        <input type="number" id="display_order" name="display_order"
                               class="admin-form-input" value="{{ old('display_order', $banner->display_order) }}" min="0">
                    </div>
                </div>

                {{-- Status --}}
                <div class="admin-form-group">
                    <label for="status" class="admin-form-label">Status</label>
                    <select id="status" name="status" class="admin-form-input admin-form-select">
                        <option value="active" {{ $banner->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $banner->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Start Date --}}
                <div class="admin-form-group">
                    <label for="start_date" class="admin-form-label">
                        Start Date <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-calendar admin-form-input-icon"></i>
                        <input type="date" id="start_date" name="start_date"
                               class="admin-form-input" value="{{ old('start_date', $banner->start_date->format('Y-m-d')) }}" required>
                    </div>
                </div>

                {{-- End Date --}}
                <div class="admin-form-group">
                    <label for="end_date" class="admin-form-label">
                        End Date <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-calendar admin-form-input-icon"></i>
                        <input type="date" id="end_date" name="end_date"
                               class="admin-form-input" value="{{ old('end_date', $banner->end_date->format('Y-m-d')) }}" required>
                    </div>
                </div>

                {{-- Image --}}
                <div class="admin-form-group admin-form-group-full">
                    <label class="admin-form-label">Banner Image</label>
                    <div class="admin-form-current-image">
                        <img src="{{ asset('storage/' . $banner->image) }}"
                             alt="{{ $banner->title }}"
                             class="admin-form-current-img-banner">
                        <div>
                            <span class="admin-form-current-label">Current image</span>
                            <p class="admin-form-current-name">{{ $banner->title }}</p>
                        </div>
                    </div>
                    <div class="admin-form-image-upload">
                        <img id="imagePreview" src="#" alt="New preview"
                             class="admin-form-image-preview admin-form-image-preview-banner" style="display: none;">
                        <div class="admin-form-image-placeholder admin-form-image-placeholder-banner" id="imagePlaceholder">
                            <i class="fas fa-image"></i>
                            <span>New image preview</span>
                        </div>
                        <label for="image" class="admin-form-image-btn">
                            <i class="fas fa-upload"></i> Change Image
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
                    <i class="fas fa-check"></i> Update Banner
                </button>
                <a href="{{ route('admin.banners.index') }}" class="admin-btn admin-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/form.js') }}"></script>
@endpush