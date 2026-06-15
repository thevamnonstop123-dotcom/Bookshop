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

            {{-- Email Form (FIRST) --}}
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

            {{-- Divider --}}
            <div class="auth-divider">
                <span class="auth-divider-line"></span>
                <span class="auth-divider-text">OR</span>
                <span class="auth-divider-line"></span>
            </div>

            {{-- Social Login Buttons (AT THE BOTTOM) --}}
            <div class="social-buttons">
                <p class="social-label">Join with your favorite account</p>
                <div class="social-buttons-grid">
                    @if (Route::has('login.google'))
                        <a href="{{ route('login.google') }}" class="social-btn social-btn-google">
                            <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Google
                        </a>
                    @endif

                    @if (Route::has('login.facebook'))
                        <a href="{{ route('login.facebook') }}" class="social-btn social-btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                            Facebook
                        </a>
                    @endif

                    {{-- Optional Twitter --}}
                    {{-- <a href="#" class="social-btn social-btn-twitter">
                        <i class="fab fa-x-twitter"></i>
                        Twitter
                    </a> --}}
                </div>
            </div>

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

            {{-- Register Form --}}
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

            {{-- Divider --}}
            <div class="auth-divider">
                <span class="auth-divider-line"></span>
                <span class="auth-divider-text">OR</span>
                <span class="auth-divider-line"></span>
            </div>

            {{-- Social Buttons at BOTTOM for Register --}}
            <div class="social-buttons">
                <p class="social-label">Join with your favorite account</p>
                <div class="social-buttons-grid">
                    @if (Route::has('login.google'))
                        <a href="{{ route('login.google') }}" class="social-btn social-btn-google">
                            <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Google
                        </a>
                    @endif

                    @if (Route::has('login.facebook'))
                        <a href="{{ route('login.facebook') }}" class="social-btn social-btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                            Facebook
                        </a>
                    @endif
                </div>
            </div>

            <p class="auth-modal-footer">
                Already have an account? 
               <a href="#" onclick="window.switchToLogin(event)">Sign in</a>
            </p>
        </div>
    </div>
</div>