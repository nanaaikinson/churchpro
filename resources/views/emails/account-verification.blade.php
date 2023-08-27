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
    <title>Complete Your Account Creation - Verification Code Inside</title>
  </head>
  <body>
    <p>Dear {{ $name }},</p>
    <p>Congratulations and welcome to {{ $appName }}! We're delighted to have you on board. You're just one step away from unlocking the full potential of your account. To finalize your account creation, we need to verify your email address.</p>
    <p>Please use the following verification code:</p>
    <p>Verification Code: <span>{{ $code }}</span></p>
    <p>Alternatively, you can simply click on the following link to be automatically verified: <a href="[Verification URL]">[Verification URL]</a></p>
    <p>This step helps ensure the security of your account and provides you with seamless access to all the necessary features you signed up for. If you didn't sign up for an account, or if you have any questions, please feel free to contact our support team at {{ $support }}.</p>
    <p>Thank you for choosing us.</p>
    <p>Best regards, <br />{{ $appName }} Team</p>
  </body>
</html>
