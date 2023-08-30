<?php

namespace App\Actions\Auth;

use App\Http\Resources\AuthUserResource;
use App\Models\User;

class Helper
{
  public static function userWithToken(User $user, string $channel, bool $refresh = false): array
  {
    $data = array_merge([
      'user' => self::user($user),
      'token' => self::withToken(
        user: $user,
        channel: $channel,
        refresh: $refresh
      )
    ]);

    return $data;
  }

  private static function withToken(User $user, string $channel, bool $refresh = false): array
  {
    $claims = ['channel' => $channel];
    $token = auth('api')->claims($claims)->setTTL(120)->login($user);
    $data = ['access_token' => $token, 'expires_at' => now()->addDay()];

    if ($refresh) {
      $refresh = auth('api')->claims($claims)->setTTL(1440)->login($user);

      $data['refresh_token'] = $refresh;
    }

    return $data;
  }

  public static function user(User $user)
  {
    return AuthUserResource::make($user);
  }
}
