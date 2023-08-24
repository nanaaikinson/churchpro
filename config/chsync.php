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
  ]
];