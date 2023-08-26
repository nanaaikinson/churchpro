<?php

return [
  'urls' => [
    'webapp' => env('WEBAPP_URL'),
  ],
  'jwt' => [
    'ttl' => [
      'access' => (int) env('JWT_MOBILE_TTL_ACCESS', 60 * 2),
      'refresh' => (int) env('JWT_MOBILE_TTL_REFRESH', 60 * 24),
    ]
  ],
  'emails' => [
    'support' => env('MAIL_SUPPORT_ADDRESS', ''),
    'no-reply' => env('MAIL_NO_REPLY_ADDRESS', ''),
    'admin' => env('MAIL_ADMIN_ADDRESS', ''),
    'info' => env('MAIL_INFO_ADDRESS', 'info@mail.aikintech.com')
  ]
];
