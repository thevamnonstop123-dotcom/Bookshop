@extends('layouts.admin')

@section('title', 'Add Banner - Bookshop Admin')
@section('page_title', 'Add New Banner')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <div class="form-container" style="max-width: 600px; background: var(--color-white); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}" placeholder="e.g., Back to School Sale" required>
                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="3" placeholder="Brief description">{{ old('description') }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Banner Image</label>
                <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png" required onchange="previewImage(event)">
                <small style="color: var(--color-text-muted); font-size: 12px;">Max 2MB. JPG, JPEG, PNG only.</small>
                @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                <div style="margin-top: 10px;">
                    <img id="imagePreview" src="#" alt="Preview" style="display: none; max-width: 200px; border-radius: 6px;">
                </div>
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-control @error('display_order') is-invalid @enderror"
                           value="{{ old('display_order', 0) }}" min="0">
                    @error('display_order') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                           value="{{ old('start_date') }}" required>
                    @error('start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                           value="{{ old('end_date') }}" required>
                    @error('end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex gap-10 mt-20">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Banner
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