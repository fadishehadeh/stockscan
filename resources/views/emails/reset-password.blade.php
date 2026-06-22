@component('mail::message')
# Reset Your Password

Hi {{ $userName }},

Click the button below to reset your password:

@component('mail::button', ['url' => $resetUrl])
Reset Password
@endcomponent

This link will expire in 1 hour.

If you didn't request a password reset, please ignore this email.

Thanks,<br>
StockScan Team
@endcomponent
