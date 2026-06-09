@extends('layouts.admin')

@section('title', 'Edit Author - Bookshop Admin')
@section('page_title', 'Edit Author')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <div class="form-container" style="max-width: 600px; background: var(--color-white); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.authors.update', $author) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Author Name</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $author->name) }}" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="bio" class="form-label">Biography</label>
                <textarea id="bio" name="bio" class="form-control @error('bio') is-invalid @enderror"
                          rows="4">{{ old('bio', $author->bio) }}</textarea>
                @error('bio') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Current Photo</label>
                <div style="margin-bottom: 10px;">
                    <img src="{{ $author->image ? asset('storage/' . $author->image) : 'https://ui-avatars.com/api/?name=' . urlencode($author->name) . '&background=f59e0b&color=fff&size=100' }}"
                         alt="{{ $author->name }}"
                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                </div>
                <label for="image" class="form-label">Change Photo (optional)</label>
                <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                <small style="color: var(--color-text-muted); font-size: 12px;">Max 2MB. Leave blank to keep current.</small>
                @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                <div style="margin-top: 10px;">
                    <img id="imagePreview" src="#" alt="Preview" style="display: none; width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="active" {{ $author->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $author->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="d-flex gap-10 mt-20">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Author
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