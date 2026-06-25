<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New StockScan Inquiry</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6;">
    <h1 style="font-size: 20px; margin-bottom: 16px;">New StockScan Inquiry</h1>

    <p><strong>Name:</strong> {{ $payload['name'] }}</p>
    <p><strong>Email:</strong> {{ $payload['email'] }}</p>
    <p><strong>Phone:</strong> {{ $payload['phone'] ?: 'Not provided' }}</p>
    <p><strong>Company:</strong> {{ $payload['company'] ?: 'Not provided' }}</p>
    <p><strong>Business Type:</strong> {{ $payload['business_type'] ?: 'Not provided' }}</p>
    <p><strong>Submitted At:</strong> {{ $payload['submitted_at'] }}</p>
    <p><strong>IP Address:</strong> {{ $payload['ip'] }}</p>

    <hr style="margin: 24px 0; border: 0; border-top: 1px solid #dbe4ee;">

    <p><strong>Message:</strong></p>
    <div style="white-space: pre-line; border: 1px solid #dbe4ee; background: #f8fafc; padding: 16px; border-radius: 8px;">
        {{ $payload['message'] }}
    </div>
</body>
</html>
