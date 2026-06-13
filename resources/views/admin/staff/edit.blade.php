@extends('layouts.admin')

@section('title', 'Edit Staff — Bookshop Admin')
@section('page_title', 'Edit Staff')

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
                <h2 class="admin-form-card-title">Edit Staff</h2>
                <p class="admin-form-card-subtitle">Update information for <strong>{{ $staff->name }}</strong></p>
            </div>
        </div>

        <form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf
            @method('PUT')

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
                               value="{{ old('name', $staff->name) }}" required>
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
                               value="{{ old('email', $staff->email) }}" required>
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
                               value="{{ old('phone', $staff->phone) }}" maxlength="11" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                    </div>
                    @error('phone')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Gender --}}
                <div class="admin-form-group">
                    <label for="gender" class="admin-form-label">Gender</label>
                    <select id="gender" name="gender" class="admin-form-input admin-form-select" required>
                        <option value="male" {{ $staff->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $staff->gender == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                {{-- Date of Birth --}}
                <div class="admin-form-group">
                    <label for="dob" class="admin-form-label">Date of Birth</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-cake-candles admin-form-input-icon"></i>
                        <input type="date" id="dob" name="dob"
                               class="admin-form-input" value="{{ old('dob', $staff->dob->format('Y-m-d')) }}" required>
                    </div>
                </div>

                {{-- Role --}}
                <div class="admin-form-group">
                    <label for="role_id" class="admin-form-label">Role</label>
                    <select id="role_id" name="role_id" class="admin-form-input admin-form-select" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ $staff->role_id == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="admin-form-group">
                    <label for="status" class="admin-form-label">Status</label>
                    <select id="status" name="status" class="admin-form-input admin-form-select" required>
                        <option value="active" {{ $staff->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $staff->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Address --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="address" class="admin-form-label">Address</label>
                    <textarea id="address" name="address"
                              class="admin-form-input admin-form-textarea @error('address') admin-form-input-error @enderror"
                              rows="2" required>{{ old('address', $staff->address) }}</textarea>
                    @error('address')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="admin-form-group admin-form-group-full">
                    <label for="password" class="admin-form-label">Password</label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-lock admin-form-input-icon"></i>
                        <input type="password" id="password" name="password"
                               class="admin-form-input @error('password') admin-form-input-error @enderror"
                               placeholder="Leave blank to keep current" minlength="8">
                    </div>
                    <span class="admin-form-hint">Only fill in to change password</span>
                    @error('password')
                        <span class="admin-form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Photo --}}
                <div class="admin-form-group admin-form-group-full">
                    <label class="admin-form-label">Profile Photo</label>
                    @php
                        $currentStaffImg = ($staff->image && $staff->image !== 'default.png')
                            ? asset('storage/' . $staff->image)
                            : null;
                    @endphp
                    @if ($currentStaffImg)
                        <div class="admin-form-current-image">
                            <img src="{{ $currentStaffImg }}" alt="{{ $staff->name }}"
                                 class="admin-form-current-img">
                            <div>
                                <span class="admin-form-current-label">Current photo</span>
                                <p class="admin-form-current-name">{{ $staff->name }}</p>
                            </div>
                        </div>
                    @endif
                    <div class="admin-form-image-upload">
                        <img id="imagePreview" src="#" alt="New preview"
                             class="admin-form-image-preview" style="display: none;">
                        <div class="admin-form-image-placeholder" id="imagePlaceholder">
                            <i class="fas fa-camera"></i>
                            <span>New photo</span>
                        </div>
                        <label for="image" class="admin-form-image-btn">
                            <i class="fas fa-upload"></i> Change Photo
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
                    <i class="fas fa-check"></i> Update Staff
                </button>
                <a href="{{ route('admin.staff.index') }}" class="admin-btn admin-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/form.js') }}"></script>
@endpush