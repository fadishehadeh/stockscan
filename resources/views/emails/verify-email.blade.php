<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f97316; color: white; padding: 20px; text-align: center; border-radius: 4px; }
        .content { background: #f9fafb; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .button { display: inline-block; background: #f97316; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; margin: 20px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Verify Your Email Address</h2>
        </div>

        <div class="content">
            <p>Hi {{ $userName }},</p>

            <p>Please verify your email address by clicking the button below:</p>

            <p><a href="{{ $verificationUrl }}" class="button">Verify Email</a></p>

            <p><strong>This link will expire in 24 hours.</strong></p>

            <p>If you didn't create this account, please ignore this email.</p>
        </div>

        <div class="footer">
            <p>StockScan Team</p>
        </div>
    </div>
</body>
</html>
