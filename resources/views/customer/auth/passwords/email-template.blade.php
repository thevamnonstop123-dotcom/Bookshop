<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password — Bookshop</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, sans-serif;
            background: #F8FAFC;
            margin: 0;
            padding: 40px 20px;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            max-width: 520px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 32px rgba(0, 0, 0, 0.06);
            border: 1px solid #E2E8F0;
        }
        .email-header {
            background: #0F172A;
            padding: 32px;
            text-align: center;
        }
        .email-logo {
            font-size: 22px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.3px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .email-logo span {
            color: #10B981;
        }
        .email-body {
            padding: 36px 32px;
        }
        .email-body h2 {
            font-size: 20px;
            font-weight: 700;
            color: #0F172A;
            margin: 0 0 12px;
            letter-spacing: -0.3px;
        }
        .email-body p {
            font-size: 14px;
            color: #475569;
            line-height: 1.7;
            margin: 0 0 16px;
        }
        .email-body .highlight {
            font-weight: 600;
            color: #0F172A;
        }
        .email-btn-wrapper {
            text-align: center;
            margin: 28px 0;
        }
        .email-btn {
            display: inline-block;
            background: #10B981;
            color: #FFFFFF;
            padding: 14px 36px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
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
        .email-divider {
            height: 1px;
            background: #E2E8F0;
            margin: 24px 0;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
            padding: 0 32px 32px;
        }
        .email-footer a {
            color: #64748B;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="email-logo">
                <i class="fas fa-book-open" style="font-size:20px;"></i> Book<span>shop</span>
            </div>
        </div>
        <div class="email-body">
            <h2>Reset Your Password</h2>
            <p>Hello <span class="highlight">{{ $name }}</span>,</p>
            <p>We received a request to reset the password for your Bookshop account. Click the button below to create a new password:</p>
            <div class="email-btn-wrapper">
                <a href="{{ $resetLink }}" class="email-btn">Reset Password</a>
            </div>
            <p style="font-size:12px; color:#94A3B8;">If the button does not work, copy and paste this link into your browser:</p>
            <div class="email-link-text">{{ $resetLink }}</div>
            <div class="email-divider"></div>
            <p style="font-size:12px; color:#94A3B8;">This link will expire in <strong>60 minutes</strong>. If you did not request a password reset, you can safely ignore this email.</p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} Bookshop. All rights reserved.</p>
            <p>Your premium online bookstore.</p>
        </div>
    </div>
</body>
</html>