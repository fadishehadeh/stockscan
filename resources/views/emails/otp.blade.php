<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f97316; color: white; padding: 20px; text-align: center; border-radius: 4px; }
        .content { background: #f9fafb; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .code { background: white; border: 2px solid #f97316; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 4px; margin: 20px 0; border-radius: 4px; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>StockScan Login Code</h2>
        </div>

        <div class="content">
            <p>Hi {{ $userName }},</p>

            <p>Your login verification code is:</p>

            <div class="code">{{ $otp }}</div>

            <p><strong>This code will expire in 5 minutes.</strong></p>

            <p>If you didn't request this code, please ignore this email.</p>
        </div>

        <div class="footer">
            <p>StockScan Team</p>
        </div>
    </div>
</body>
</html>
