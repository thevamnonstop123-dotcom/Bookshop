@extends('layouts.admin')

@section('title', 'Edit Category - Bookshop Admin')
@section('page_title', 'Edit Category')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <div class="form-container" style="max-width: 560px; background: var(--color-white); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $category->name) }}" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="active" {{ $category->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $category->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="d-flex gap-10 mt-20">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>

@endsection