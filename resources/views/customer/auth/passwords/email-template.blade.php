<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; padding: 40px; }
        .email-container { max-width: 520px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .logo { text-align: center; margin-bottom: 28px; font-size: 24px; font-weight: 800; color: #0f172a; }
        .logo span { color: #f59e0b; }
        h2 { color: #1e293b; font-size: 20px; margin-bottom: 12px; }
        p { color: #475569; font-size: 14px; line-height: 1.6; }
        .btn { display: inline-block; background: #f59e0b; color: #0f172a; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 15px; margin: 20px 0; }
        .link { font-size: 12px; color: #94a3b8; word-break: break-all; }
        .footer { margin-top: 30px; font-size: 12px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="logo">Book<span>shop</span></div>
        <h2>Reset Your Password</h2>
        <p>Hello {{ $name }},</p>
        <p>We received a request to reset your password. Click the button below to create a new password:</p>
        <div style="text-align: center;">
            <a href="{{ $resetLink }}" class="btn">Reset Password</a>
        </div>
        <p>Or copy this link:</p>
        <p class="link">{{ $resetLink }}</p>
        <p>This link will expire in 60 minutes. If you didn't request this, you can safely ignore this email.</p>
        <div class="footer">
            &copy; {{ date('Y') }} Bookshop. All rights reserved.
        </div>
    </div>
</body>
</html>
