<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password — Bookshop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #F8FAFC;
            padding: 40px 20px;
        }
        .email-wrapper { max-width: 520px; margin: 0 auto; }
        .email-container {
            background: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #E2E8F0;
        }
        .email-header { background: #0F172A; padding: 32px; text-align: center; }
        .email-logo {
            font-size: 22px;
            font-weight: 800;
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .email-logo span { color: #10B981; }
        .email-body { padding: 36px 32px; }
        .email-title { font-size: 20px; font-weight: 700; color: #FFFFFF; margin-bottom: 16px; }
        .email-greeting { font-size: 14px; color: #475569; margin-bottom: 16px; }
        .email-message { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 24px; }
        .email-btn-wrapper { text-align: center; margin: 28px 0; }
        .email-btn {
            display: inline-block;
            background: #10B981;
            color: #FFFFFF;
            padding: 14px 36px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
        }
        .email-link-text {
            font-size: 12px;
            color: #94A3B8;
            word-break: break-all;
            background: #F8FAFC;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            margin: 16px 0;
        }
        .email-divider { height: 1px; background: #E2E8F0; margin: 24px 0; }
        .email-note { font-size: 12px; color: #94A3B8; line-height: 1.6; }
        .email-footer { text-align: center; padding: 0 32px 32px; font-size: 12px; color: #94A3B8; }
        .email-footer a { color: #64748B; text-decoration: none; }
        @media (max-width: 600px) {
            .email-body { padding: 28px 24px; }
            .email-header { padding: 28px 24px; }
            .email-footer { padding: 0 24px 28px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <a href="{{ route('customer.home') }}" class="email-logo">
                    📚 Book<span>shop</span>
                </a>
            </div>
            <div class="email-body">
                <h2 class="email-title">Reset Your Password</h2>
                <p class="email-greeting">Hello <strong>{{ $name ?? 'Reader' }}</strong>,</p>
                <p class="email-message">We received a request to reset the password for your Bookshop account. Click the button below to create a new password:</p>
                <div class="email-btn-wrapper">
                    <a href="{{ $resetLink }}" class="email-btn">Reset Password</a>
                </div>
                <p class="email-note">If the button doesn't work, copy this link:</p>
                <div class="email-link-text">{{ $resetLink }}</div>
                <div class="email-divider"></div>
                <p class="email-note">This link will expire in <strong>60 minutes</strong>. If you did not request this, please ignore this email.</p>
            </div>
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Bookshop. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>