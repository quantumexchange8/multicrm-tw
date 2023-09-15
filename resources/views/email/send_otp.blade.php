<x-mail::message>
<h1>OTP Verification</h1>
<p>Please enter the OTP below to complete your registration:</p>
<h2>{{ $otp }}</h2>

If you did not request this OTP, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
