<?php

namespace App\Actions\Auth;

use App\Http\Resources\AuthUserResource;
use App\Models\User;

class Helper
{
  public static function userWithToken(User $user, string $channel, bool $refresh = false, bool $relations = false): array
  {
    $data = array_merge([
      'user' => self::user($user),
      'tokens' => self::withToken(
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
    $data = ['access' => $token, 'expires_at' => now()->addDay()];

    if ($refresh) {
      $refresh = auth('api')->claims($claims)->setTTL(1440)->login($user);

      $data['refresh'] = $refresh;
    }

    return $data;
  }

  public static function user(User $user, bool $relations = false)
  {
    return AuthUserResource::make($user, $relations);
  }
}