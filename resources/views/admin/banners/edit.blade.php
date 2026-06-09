@extends('layouts.admin')

@section('title', 'Edit Banner - Bookshop Admin')
@section('page_title', 'Edit Banner')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <div class="form-container" style="max-width: 600px; background: var(--color-white); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $banner->title) }}" required>
                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="3">{{ old('description', $banner->description) }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Current Image</label>
                <div style="margin-bottom: 10px;">
                    <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}"
                         style="max-width: 200px; border-radius: 6px;">
                </div>
                <label for="image" class="form-label">Change Image (optional)</label>
                <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                <small style="color: var(--color-text-muted); font-size: 12px;">Max 2MB. Leave blank to keep current.</small>
                @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                <div style="margin-top: 10px;">
                    <img id="imagePreview" src="#" alt="Preview" style="display: none; max-width: 200px; border-radius: 6px;">
                </div>
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-control"
                           value="{{ old('display_order', $banner->display_order) }}" min="0">
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" {{ $banner->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $banner->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                           value="{{ old('start_date', $banner->start_date->format('Y-m-d')) }}" required>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                           value="{{ old('end_date', $banner->end_date->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="d-flex gap-10 mt-20">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Banner
                </button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline">Cancel</a>
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
</script>
@endpush