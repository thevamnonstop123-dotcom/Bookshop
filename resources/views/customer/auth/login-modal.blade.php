{{-- Login Modal --}}
<div class="auth-modal-overlay" id="loginModal" aria-hidden="true">
    <div class="auth-modal-container" role="dialog" aria-modal="true" aria-label="Authentication">

        <button class="auth-modal-close" onclick="window.closeLoginModal()" aria-label="Close modal">
            <i class="fas fa-xmark"></i>
        </button>

        {{-- Brand --}}
        <div class="auth-modal-brand">
            <div class="auth-modal-brand-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <span class="auth-modal-brand-text">Book<span class="auth-modal-brand-accent">shop</span></span>
        </div>

        {{-- Login Form --}}
        <div id="loginFormContent">
            <h3 class="auth-modal-title">Welcome Back</h3>
            <p class="auth-modal-subtitle">Sign in to your account</p>

            <div id="loginError" class="alert alert-danger" style="display:none;"></div>
            <div id="loginSuccess" class="alert alert-success" style="display:none;"></div>

            {{-- Social Login Buttons --}}
            <div class="social-buttons">
                @if (Route::has('login.google'))
                    <a href="{{ route('login.google') }}" class="social-btn social-btn-google">
                        <img src="https://www.google.com/favicon.ico" width="18" alt="Google"> Continue with Google
                    </a>
                @endif

                @if (Route::has('login.facebook'))
                    <a href="{{ route('login.facebook') }}" class="social-btn social-btn-facebook">
                        <i class="fab fa-facebook-f"></i> Continue with Facebook
                    </a>
                @endif
            </div>

            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                <span style="font-size:12px;color:#94a3b8;">or</span>
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            </div>

            <form id="modalLoginForm" action="{{ route('login') }}" method="POST" onsubmit="window.handleLogin(event)" class="auth-form">
                @csrf
                <div class="auth-form-group">
                    <label class="auth-form-label">Email Address</label>
                    <input type="email" name="email" class="auth-form-input" placeholder="you@example.com" required>
                </div>
                <div class="auth-form-group">
                    <label class="auth-form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" class="auth-form-input" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="window.toggleModalPass()" tabindex="-1">
                            <i class="fas fa-eye" id="modalToggleIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="auth-form-extras">
                    <label class="auth-remember">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="auth-forgot">Forgot password?</a>
                </div>
                <button type="submit" class="auth-submit-btn">
                    <i class="fas fa-arrow-right-to-bracket"></i> Sign In
                </button>
            </form>

            <p class="auth-modal-footer">
                Don't have an account? 
                <a href="#" onclick="window.switchToRegister(event)">Create one</a>
            </p>
        </div>

        {{-- Register Form --}}
        <div id="registerFormContent" style="display:none;">
            <h3 class="auth-modal-title">Create Account</h3>
            <p class="auth-modal-subtitle">Join our community of readers</p>

            <div id="registerError" class="alert alert-danger" style="display:none;"></div>

            <form id="modalRegisterForm" action="{{ route('register') }}" method="POST" onsubmit="window.handleRegister(event)" class="auth-form">
                @csrf
                <div class="auth-form-group">
                    <label class="auth-form-label">Full Name</label>
                    <input type="text" name="name" class="auth-form-input" placeholder="Your full name" required>
                </div>
                <div class="auth-form-group">
                    <label class="auth-form-label">Email Address</label>
                    <input type="email" name="email" class="auth-form-input" placeholder="you@example.com" required>
                </div>
                <div class="auth-form-group">
                    <label class="auth-form-label">Phone Number</label>
                    <input type="tel" name="phone" class="auth-form-input" placeholder="09123456789" maxlength="11" required
                           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)">
                </div>
                <div class="auth-form-row">
                    <div class="auth-form-group">
                        <label class="auth-form-label">Gender</label>
                        <select name="gender" class="auth-form-input" required>
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="auth-form-group">
                        <label class="auth-form-label">Date of Birth</label>
                        <input type="date" name="dob" class="auth-form-input" required>
                    </div>
                </div>
                <div class="auth-form-group">
                    <label class="auth-form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" class="auth-form-input" placeholder="Min 8 characters" required minlength="8">
                        <button type="button" class="password-toggle" onclick="window.toggleModalRegPass()" tabindex="-1">
                            <i class="fas fa-eye" id="modalRegToggleIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="auth-form-group">
                    <label class="auth-form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="auth-form-input" placeholder="Re-enter password" required>
                </div>
                <button type="submit" class="auth-submit-btn auth-submit-register">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <p class="auth-modal-footer">
                Already have an account? 
               <a href="#" onclick="window.switchToLogin(event)">Sign in</a>
            </p>
        </div>
    </div>
</div>