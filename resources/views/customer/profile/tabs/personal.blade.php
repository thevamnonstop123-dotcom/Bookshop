<div class="profile-card">
    <div class="profile-card-header"><h3 class="profile-card-title">Personal Information</h3></div>
    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
        @csrf @method('PUT')
        <div class="profile-form-grid-3">
            <div class="profile-form-group"><label class="profile-label">Full Name</label><input type="text" name="name" class="profile-input" value="{{ old('name', $customer->name) }}" required></div>
            <div class="profile-form-group"><label class="profile-label">Phone Number</label><input type="tel" name="phone" class="profile-input" value="{{ old('phone', $customer->phone) }}" maxlength="11" required></div>
            <div class="profile-form-group"><label class="profile-label">Date of Birth</label><input type="date" name="dob" class="profile-input" value="{{ old('dob', $customer->dob ? $customer->dob->format('Y-m-d') : '') }}" required></div>
            <div class="profile-form-group"><label class="profile-label">Gender</label><select name="gender" class="profile-input profile-select" required><option value="male" {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>Female</option></select></div>
        </div>
        <div class="profile-form-actions"><button type="submit" class="profile-btn profile-btn-primary"><i class="fas fa-check"></i> Save Changes</button></div>
    </form>
</div>
<div class="profile-card">
    <div class="profile-card-header"><h3 class="profile-card-title">Profile Photo</h3></div>
    <div class="profile-photo-section">
        <div class="profile-photo-preview"><img src="{{ $customer->image && $customer->image !== 'default.png' ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=1E3A8A&color=fff&size=200' }}" alt="Photo" class="profile-photo-img" id="photoPreview"><div class="profile-photo-overlay" onclick="document.getElementById('photoInput').click()"><i class="fas fa-camera"></i><span>Change Photo</span></div></div>
        <div class="profile-photo-info"><p class="profile-photo-hint">JPG, PNG or WEBP. Max 2MB.</p><button class="profile-btn profile-btn-outline" onclick="document.getElementById('photoInput').click()"><i class="fas fa-upload"></i> Upload Photo</button><input type="file" id="photoInput" accept=".jpg,.jpeg,.png,.webp" onchange="uploadPhoto(event)" style="display:none;"></div>
    </div>
</div>
