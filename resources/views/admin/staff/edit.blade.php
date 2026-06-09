@extends('layouts.admin')

@section('title', 'Edit Staff - Bookshop Admin')
@section('page_title', 'Edit Staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

    <div class="form-container" style="max-width: 620px; background: var(--color-white); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $staff->name) }}" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $staff->email) }}" required>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $staff->phone) }}" maxlength="11" required
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                    @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                          rows="2" required>{{ old('address', $staff->address) }}</textarea>
                @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="male" {{ $staff->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $staff->gender == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" id="dob" name="dob" class="form-control"
                           value="{{ old('dob', $staff->dob->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="d-flex gap-20">
                <div class="form-group" style="flex: 1;">
                    <label for="role_id" class="form-label">Role</label>
                    <select id="role_id" name="role_id" class="form-control" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ $staff->role_id == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" {{ $staff->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $staff->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="Minimum 8 characters">
                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                {{-- Show current image --}}
                @php
                    $currentStaffImg = ($staff->image && $staff->image !== 'default.png')
                        ? asset('storage/' . $staff->image)
                        : null;
                @endphp
                @if ($currentStaffImg)
                    <label class="form-label">Current Photo</label>
                    <div style="margin-bottom: 10px;">
                        <img src="{{ $currentStaffImg }}" alt="{{ $staff->name }}"
                             style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-border);">
                    </div>
                @endif

                <label for="image" class="form-label">Change Photo (optional)</label>
                <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                <small style="color: var(--color-text-muted); font-size: 12px;">Max 2MB. Leave blank to keep current.</small>
                @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                <div style="margin-top: 10px;">
                    <img id="imagePreview" src="#" alt="Preview" style="display: none; width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                </div>
            </div>

            <div class="d-flex gap-10 mt-20">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Staff
                </button>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-outline">Cancel</a>
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