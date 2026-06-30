<div class="profile-card">
    <div class="profile-card-header"><h3 class="profile-card-title">Email Address</h3></div>
    <div class="profile-security-row">
        <div class="profile-security-info"><span class="profile-security-label">Current Email</span><span class="profile-security-value">{{ $customer->email }}</span></div>
        <button class="profile-btn profile-btn-outline" onclick="openEmailModal()"><i class="fas fa-pen"></i> Change Email</button>
    </div>
</div>
<div class="profile-card">
    <div class="profile-card-header"><h3 class="profile-card-title">Password & Security</h3></div>
    <form action="{{ route('customer.profile.password') }}" method="POST" class="profile-form" id="passwordForm">
        @csrf
        <input type="text" style="display:none;" autocomplete="username">
        <input type="password" style="display:none;" autocomplete="current-password">
        <div class="profile-form-group"><label class="profile-label">Current Password</label><div class="profile-input-password"><input type="password" name="current_password" class="profile-input" required autocomplete="new-password"><button type="button" class="profile-password-toggle" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button></div></div>
        <div class="profile-form-group"><label class="profile-label">New Password</label><div class="profile-input-password"><input type="password" name="password" class="profile-input" id="newPassword" required minlength="8" autocomplete="new-password"><button type="button" class="profile-password-toggle" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button></div></div>
        <div class="profile-form-group"><label class="profile-label">Confirm Password</label><div class="profile-input-password"><input type="password" name="password_confirmation" class="profile-input" required autocomplete="new-password"><button type="button" class="profile-password-toggle" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button></div></div>
        <div class="profile-password-strength" id="passwordStrength" style="display:none;"><div class="profile-strength-bar"><div class="profile-strength-fill" id="strengthFill"></div></div><ul class="profile-strength-requirements"><li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li><li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter</li><li id="req-number"><i class="fas fa-circle"></i> One number</li><li id="req-special"><i class="fas fa-circle"></i> One special character</li></ul></div>
        <div class="profile-form-actions"><button type="submit" class="profile-btn profile-btn-primary"><i class="fas fa-lock"></i> Change Password</button></div>
    </form>
</div>
