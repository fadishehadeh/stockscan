@component('mail::message')
# Verify Your Email Address

Hi {{ $userName }},

Please verify your email address by clicking the button below:

@component('mail::button', ['url' => $verificationUrl])
Verify Email
@endcomponent

This link will expire in 24 hours.

If you didn't create this account, please ignore this email.

Thanks,<br>
StockScan Team
@endcomponent
