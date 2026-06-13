<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, sans-serif;
            background: #F1F5F9;
            margin: 0;
            padding: 40px 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .email-wrapper {
            max-width: 560px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid #E2E8F0;
        }
        .email-header {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 100%);
            padding: 40px 36px;
            text-align: center;
        }
        .email-logo {
            font-size: 24px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.4px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .email-logo-accent {
            color: #10B981;
        }
        .email-header-subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 8px;
            font-weight: 500;
        }
        .email-body {
            padding: 36px;
        }
        .email-greeting {
            font-size: 15px;
            color: #0F172A;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .email-content {
            font-size: 14px;
            color: #475569;
            line-height: 1.8;
        }
        .email-divider {
            height: 1px;
            background: #F1F5F9;
            margin: 28px 0;
        }
        .email-footer {
            padding: 0 36px 32px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
            line-height: 1.7;
        }
        .email-footer p {
            margin: 0;
        }
        .email-footer-legal {
            font-size: 10px;
            color: #CBD5E1;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="email-logo">
                <i class="fas fa-book-open" style="font-size:20px;"></i> Book<span class="email-logo-accent">shop</span>
            </div>
            <div class="email-header-subtitle">Your Premium Online Bookstore</div>
        </div>

        <div class="email-body">
            <div class="email-greeting">Hello {{ $name }},</div>
            <div class="email-content">
                {!! nl2br(e($content)) !!}
            </div>
        </div>

        <div class="email-divider"></div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} Bookshop. All rights reserved.</p>
            <p class="email-footer-legal">
                You received this email because you are a Bookshop customer.
                If you no longer wish to receive promotional emails, please contact support.
            </p>
        </div>
    </div>
</body>
</html>