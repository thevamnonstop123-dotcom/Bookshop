@extends('layouts.admin')

@section('title', 'Add Author - Bookshop Admin')
@section('page_title', 'Add New Author')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <div class="form-container" style="max-width: 600px; background: var(--color-white); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Author Name</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g., James Clear" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="bio" class="form-label">Biography</label>
                <textarea id="bio" name="bio" class="form-control @error('bio') is-invalid @enderror"
                          rows="4" placeholder="Brief biography of the author">{{ old('bio') }}</textarea>
                @error('bio') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Author Photo</label>
                <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png" required onchange="previewImage(event)">
                <small style="color: var(--color-text-muted); font-size: 12px;">Max 2MB. JPG, JPEG, PNG only.</small>
                @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                <div style="margin-top: 10px;">
                    <img id="imagePreview" src="#" alt="Preview" style="display: none; width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex gap-10 mt-20">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Author
                </button>
                <a href="{{ route('admin.authors.index') }}" class="btn btn-outline">Cancel</a>
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