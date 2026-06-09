{{-- Login Modal Overlay --}}
<div class="modal-overlay" id="loginModal" style="display:none;">
    <div class="modal-container">
        <button class="modal-close" onclick="closeLoginModal()">
            <i class="fas fa-times"></i>
        </button>

        <div class="modal-brand">
            <div class="brand-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h2>Book<span>shop</span></h2>
        </div>

        <div id="loginFormContent">
            <h3 class="modal-title">Welcome Back</h3>
            <p class="modal-subtitle">Sign in to your account</p>

            <div id="loginError" class="alert alert-danger" style="display:none;"></div>
            <div id="loginSuccess" class="alert alert-success" style="display:none;"></div>

            <form id="modalLoginForm" onsubmit="handleLogin(event)">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="toggleModalPass()" tabindex="-1">
                            <i class="fas fa-eye" id="modalToggleIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="customer-login-extras">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <p class="modal-footer-text">
                Don't have an account? 
                <a href="#" onclick="switchToRegister(event)">Create one</a>
            </p>
        </div>

        {{-- Register Form (hidden by default) --}}
        <div id="registerFormContent" style="display:none;">
            <h3 class="modal-title">Create Account</h3>
            <p class="modal-subtitle">Join our community of readers</p>

            <div id="registerError" class="alert alert-danger" style="display:none;"></div>

            <form id="modalRegisterForm" onsubmit="handleRegister(event)">
                @csrf
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Your full name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" placeholder="09123456789" maxlength="11" required
                           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)">
                </div>
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required minlength="8">
                        <button type="button" class="password-toggle" onclick="toggleModalRegPass()" tabindex="-1">
                            <i class="fas fa-eye" id="modalRegToggleIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter password" required>
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <p class="modal-footer-text">
                Already have an account? 
                <a href="#" onclick="switchToLogin(event)">Sign in</a>
            </p>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15,23,42,0.7);
        backdrop-filter: blur(4px);
        z-index: 5000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.25s ease;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .modal-container {
        background: var(--color-white);
        border-radius: var(--radius-xl);
        padding: 40px;
        max-width: 440px;
        width: 92%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        animation: scaleIn 0.3s cubic-bezier(0.175,0.885,0.32,1.275);
    }
    @keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-close {
        position: absolute;
        top: 16px; right: 16px;
        width: 34px; height: 34px;
        border-radius: 50%;
        border: 1.5px solid var(--color-border);
        background: var(--color-white);
        cursor: pointer;
        font-size: 16px;
        color: var(--color-text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-fast);
    }
    .modal-close:hover { background: #fef2f2; border-color: #fecaca; color: var(--color-danger); }
    .modal-brand { text-align: center; margin-bottom: 6px; }
    .modal-brand .brand-icon {
        width: 46px; height: 46px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 20px; color: #0f172a;
        margin-bottom: 12px;
        box-shadow: 0 4px 16px rgba(245,158,11,0.3);
    }
    .modal-brand h2 { font-size: 20px; font-weight: 800; color: var(--color-primary); }
    .modal-brand span { color: var(--color-accent); }
    .modal-title { text-align: center; font-size: 18px; font-weight: 600; margin-bottom: 4px; color: var(--color-text); }
    .modal-subtitle { text-align: center; font-size: 13px; color: var(--color-text-muted); margin-bottom: 28px; }
    .modal-container .form-group { margin-bottom: 16px; }
    .modal-container .form-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-muted); }
    .modal-container .form-control { padding: 11px 14px; font-size: 14px; background: var(--color-surface); border-radius: var(--radius-sm); }
    .modal-container .password-wrapper { position: relative; }
    .modal-container .password-wrapper .form-control { padding-right: 42px; }
    .modal-container .password-toggle {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer; color: var(--color-text-muted); font-size: 15px;
    }
    .modal-footer-text { text-align: center; margin-top: 20px; font-size: 13px; color: var(--color-text-muted); }
    .modal-footer-text a { font-weight: 600; color: var(--color-accent-dark); }
    .customer-login-extras {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; font-size: 13px;
    }
    .remember-me { display: flex; align-items: center; gap: 6px; cursor: pointer; color: var(--color-text-secondary); }
    .remember-me input { accent-color: var(--color-accent); }
    .forgot-password { font-size: 13px; color: var(--color-accent-dark); font-weight: 500; }
</style>

<script>
    function openLoginModal() {
        document.getElementById('loginModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeLoginModal() {
        document.getElementById('loginModal').style.display = 'none';
        document.body.style.overflow = '';
    }
    function switchToRegister(e) {
        e.preventDefault();
        document.getElementById('loginFormContent').style.display = 'none';
        document.getElementById('registerFormContent').style.display = 'block';
    }
    function switchToLogin(e) {
        e.preventDefault();
        document.getElementById('registerFormContent').style.display = 'none';
        document.getElementById('loginFormContent').style.display = 'block';
    }
    function toggleModalPass() {
        const pass = document.querySelector('#modalLoginForm input[name="password"]');
        const icon = document.getElementById('modalToggleIcon');
        if (pass.type === 'password') { pass.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
        else { pass.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    }
    function toggleModalRegPass() {
        const pass = document.querySelector('#modalRegisterForm input[name="password"]');
        const icon = document.getElementById('modalRegToggleIcon');
        if (pass.type === 'password') { pass.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
        else { pass.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    }

    async function handleLogin(e) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';

        try {
            const resp = await fetch('{{ route("login") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: form.email.value,
                    password: form.password.value,
                    remember: form.remember.checked
                })
            });

            if (resp.ok) {
                window.location.reload();
            } else {
                const data = await resp.json();
                document.getElementById('loginError').style.display = 'block';
                document.getElementById('loginError').innerHTML = data.message || 'Invalid credentials';
            }
        } catch (err) {
            document.getElementById('loginError').style.display = 'block';
            document.getElementById('loginError').innerHTML = 'Something went wrong.';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
    }

    async function handleRegister(e) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

        try {
            const resp = await fetch('{{ route("register") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: form.name.value,
                    email: form.email.value,
                    phone: form.phone.value,
                    gender: form.gender.value,
                    dob: form.dob.value,
                    password: form.password.value,
                    password_confirmation: form.password_confirmation.value
                })
            });

            if (resp.ok) {
                window.location.reload();
            } else {
                const data = await resp.json();
                document.getElementById('registerError').style.display = 'block';
                document.getElementById('registerError').innerHTML = data.message || Object.values(data.errors||{}).flat().join('<br>');
            }
        } catch (err) {
            document.getElementById('registerError').style.display = 'block';
            document.getElementById('registerError').innerHTML = 'Something went wrong.';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLoginModal();
    });
    // Close on overlay click
    document.getElementById('loginModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeLoginModal();
    });
</script>
