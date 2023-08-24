@php
  $appName = config('app.name');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirm your email for your {{ $appName }} account</title>
</head>
<body>
  <p>Hi, {{ $user->name }}</p>
  <p>Thank you for choosing {{ $appName }}.</p>
  <p>To ensure the security of your account and provide you with the best possible experience, we kindly request you to verify your account by following the simple steps below:</p>

  <p>Click on the following link to verify your account: Verify Account</p>
  <p>Once you click the link, you will be directed to a verification page. Please follow the instructions provided to complete the verification process.</p>
  <p>After verifying your account, you will gain access to all the exclusive features and benefits that {{ $appName }} has to offer. We're committed to making your experience exceptional.</p>
  <p>If you did not create an account with {{ $appName }}, please ignore this email. Your account will not be verified without your confirmation.</p>

  <p>For any assistance or inquiries, please don't hesitate to contact our dedicated support team at [Support Email] or [Support Phone Number].</p>
  <p>Thank you for choosing {{ $appName }}. We look forward to serving you and providing you with an amazing experience.</p>
</body>
</html>
