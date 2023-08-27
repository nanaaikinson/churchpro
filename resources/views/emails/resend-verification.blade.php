@php
  $appName = config('app.name');
  $support = config('chsync.emails.support');
@endphp

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification Code - Complete Your Account Creation</title>
  </head>
  <body>
    <p>Hello {{ $name }},</p>
    <p>We hope this email finds you well. We noticed that you're in the process of creating your account with us, and we're excited to have you on board.</p>
    <p>It seems like you might need a new verification code to finalize your account setup. No worries – we've got you covered!</p>
    <p>To proceed, please click on the following verification link or use the provided verification code if prompted:</p>
    <p>Verification Code: <span>{{ $code }}</span></p>
    <p>Alternatively, you can simply click on the following link to be automatically verified: <a href="[Verification URL]">[Verification URL]</a></p>
    <p>If you encounter any issues or have questions, don't hesitate to reach out to our support team at {{ $support }}. We're here to assist you!</p>
    <p>Thank you for choosing us.</p>
    <p>Best regards, <br />{{ $appName }} Team</p>
  </body>
</html>
