@extends('layouts.admin')

@section('title', 'Add Staff — Bookshop Admin')
@section('page_title', 'Add New Staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <a href="{{ route('admin.staff.index') }}" class="admin-form-back">
        <i class="fas fa-arrow-left"></i> Back to Staff
    </a>

    <div class="admin-form-card">
        <div class="admin-form-card-header">
            <div class="admin-form-card-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h2 class="admin-form-card-title">Staff Information</h2>
                <p class="admin-form-card-subtitle">Create a new staff account with role-based permissions</p>
            </div>
        </div>

        <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf

            <div class="admin-form-grid">
                {{-- Name --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="name" class="admin-form-label">
                        Full Name <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-user admin-form-input-icon"></i>
                        <input type="text" id="name" name="name"
                               class="admin-form-input @error('name') admin-form-input-error @enderror"
                               value="{{ old('name') }}" placeholder="Staff full name" required autofocus>
                    </div>
                    @error('name')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="admin-form-group">
                    <label for="email" class="admin-form-label">
                        Email Address <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-envelope admin-form-input-icon"></i>
                        <input type="email" id="email" name="email"
                               class="admin-form-input @error('email') admin-form-input-error @enderror"
                               value="{{ old('email') }}" placeholder="staff@bookshop.com" required>
                    </div>
                    @error('email')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="admin-form-group">
                    <label for="phone" class="admin-form-label">
                        Phone Number <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-phone admin-form-input-icon"></i>
                        <input type="tel" id="phone" name="phone"
                               class="admin-form-input @error('phone') admin-form-input-error @enderror"
                               value="{{ old('phone') }}" maxlength="11" placeholder="09123456789" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                    </div>
                    @error('phone')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Gender --}}
                <div class="admin-form-group">
                    <label for="gender" class="admin-form-label">
                        Gender <span class="admin-form-required">*</span>
                    </label>
                    <select id="gender" name="gender"
                            class="admin-form-input admin-form-select @error('gender') admin-form-input-error @enderror" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Date of Birth --}}
                <div class="admin-form-group">
                    <label for="dob" class="admin-form-label">
                        Date of Birth <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-cake-candles admin-form-input-icon"></i>
                        <input type="date" id="dob" name="dob"
                               class="admin-form-input @error('dob') admin-form-input-error @enderror"
                               value="{{ old('dob') }}" required>
                    </div>
                    @error('dob')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="admin-form-group">
                    <label for="role_id" class="admin-form-label">
                        Role <span class="admin-form-required">*</span>
                    </label>
                    <select id="role_id" name="role_id"
                            class="admin-form-input admin-form-select @error('role_id') admin-form-input-error @enderror" required>
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="admin-form-group">
                    <label for="status" class="admin-form-label">Status</label>
                    <select id="status" name="status"
                            class="admin-form-input admin-form-select @error('status') admin-form-input-error @enderror">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="address" class="admin-form-label">
                        Address <span class="admin-form-required">*</span>
                    </label>
                    <textarea id="address" name="address"
                              class="admin-form-input admin-form-textarea @error('address') admin-form-input-error @enderror"
                              rows="2" placeholder="Staff address" required>{{ old('address') }}</textarea>
                    @error('address')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="password" class="admin-form-label">
                        Password <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-lock admin-form-input-icon"></i>
                        <input type="password" id="password" name="password"
                               class="admin-form-input @error('password') admin-form-input-error @enderror"
                               placeholder="Minimum 8 characters" required minlength="8">
                    </div>
                    @error('password')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Photo --}}
                <div class="admin-form-group admin-form-group-full">
                    <label class="admin-form-label">Profile Photo</label>
                    <div class="admin-form-image-upload">
                        <img id="imagePreview" src="#" alt="Preview"
                             class="admin-form-image-preview" style="display: none;">
                        <div class="admin-form-image-placeholder" id="imagePlaceholder">
                            <i class="fas fa-camera"></i>
                            <span>No photo</span>
                        </div>
                        <label for="image" class="admin-form-image-btn">
                            <i class="fas fa-upload"></i> Choose Photo
                        </label>
                        <input type="file" id="image" name="image"
                               class="admin-form-input-file @error('image') admin-form-input-error @enderror"
                               accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                        <span class="admin-form-image-hint">Optional. JPG, JPEG, PNG. Max 2MB.</span>
                    </div>
                    @error('image')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-check"></i> Create Staff
                </button>
                <a href="{{ route('admin.staff.index') }}" class="admin-btn admin-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/form.js') }}"></script>
@endpush