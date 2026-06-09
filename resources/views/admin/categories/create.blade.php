@extends('layouts.admin')

@section('title', 'Add Category - Bookshop Admin')
@section('page_title', 'Add New Category')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <div class="form-container" style="max-width: 560px; background: var(--color-white); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g., Programming" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="3" placeholder="Brief description of this category">{{ old('description') }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
                    <i class="fas fa-save"></i> Save Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>

@endsection