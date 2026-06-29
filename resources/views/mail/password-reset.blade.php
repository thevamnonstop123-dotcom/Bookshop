<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password — Bookshop</title>
</head>
<body style="margin:0;padding:0;box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#F8FAFC;padding:40px 20px;-webkit-font-smoothing:antialiased;">
    <div style="max-width:520px;margin:0 auto;">
        <div style="background:#FFFFFF;border-radius:18px;overflow:hidden;border:1px solid #E5E7EB;">

            {{-- Header --}}
            <div style="background:#1E3A8A;padding:32px;text-align:center;">
                <div style="font-size:22px;font-weight:800;color:#FFFFFF;display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
                    📚 Book<span style="color:#FBBF24;">shop</span>
                </div>
            </div>

            {{-- Body --}}
            <div style="padding:36px 32px;">
                <h2 style="font-size:20px;font-weight:700;color:#111827;margin:0 0 16px;">Reset Your Password</h2>
                <p style="font-size:14px;color:#6B7280;margin:0 0 16px;">Hello <strong style="color:#111827;">{{ $name ?? 'Reader' }}</strong>,</p>
                <p style="font-size:14px;color:#6B7280;line-height:1.7;margin:0 0 24px;">We received a request to reset the password for your Bookshop account. Click the button below to create a new password:</p>

                {{-- Button --}}
                <div style="text-align:center;margin:28px 0;">
                    <a href="{{ $resetLink }}" style="display:inline-block;background:#1E3A8A;color:#FFFFFF;padding:14px 36px;border-radius:14px;text-decoration:none;font-weight:700;font-size:15px;">Reset Password</a>
                </div>

                <p style="font-size:12px;color:#9CA3AF;line-height:1.6;margin:0 0 16px;">If the button doesn't work, copy this link:</p>
                <div style="font-size:12px;color:#9CA3AF;word-break:break-all;background:#F8FAFC;padding:12px 16px;border-radius:10px;border:1px solid #E5E7EB;margin:16px 0;">{{ $resetLink }}</div>

                <div style="height:1px;background:#E5E7EB;margin:24px 0;"></div>
                <p style="font-size:12px;color:#9CA3AF;line-height:1.6;margin:0;">This link will expire in <strong style="color:#1E3A8A;">60 minutes</strong>. If you did not request this, please ignore this email.</p>
            </div>

            {{-- Footer --}}
            <div style="text-align:center;padding:0 32px 32px;font-size:12px;color:#9CA3AF;">
                <p style="margin:0;">&copy; {{ date('Y') }} Bookshop. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>