@component('mail::message')
# Your StockScan Login Code

Hi {{ $userName }},

Your login verification code is:

@component('mail::panel')
# {{ $otp }}
@endcomponent

This code will expire in 5 minutes.

If you didn't request this code, please ignore this email.

Thanks,<br>
StockScan Team
@endcomponent
