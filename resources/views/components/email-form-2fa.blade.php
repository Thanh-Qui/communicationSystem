<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Two Factor Authentication</title>
</head>
<body style="background: #f8f9fa; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
  <div style="background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15); max-width: 420px; margin: auto; text-align: center;">
    <h2 style="margin-bottom: 20px; font-weight: bold;">Two Factor Authentication</h2>
    <p style="font-size: 18px;">Hello <strong>{{ $user->name }}</strong>,</p>
    <p>Your 2FA code is:</p>
    <div style="font-size: 2.5rem; letter-spacing: 0.15rem; font-weight: 700; color: #0d6efd; margin: 10px 0 15px 0; user-select: all;">{{ $twoFactorCode }}</div>
    <p style="color: #6c757d; font-size: 14px;">This code will expire in a few minutes.</p>
  </div>
</body>
</html>
