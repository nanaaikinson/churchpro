<?php

namespace App\Actions\Auth;

use App\Enums\UserChannelEnum;
use App\Http\Resources\AuthUserResource;
use App\Models\User;

class Helper
{
  public static function userWithToken(User $user, string $channel, bool $refresh = false, bool $relations = false): array
  {
    return array_merge([
      'user' => self::user($user, $relations, $channel),
      'tokens' => self::withToken(
        user: $user,
        channel: $channel,
        refresh: $refresh
      )
    ]);
  }

  private static function withToken(User $user, string $channel, bool $refresh = false): array
  {
    $claims = ['channel' => $channel, 'type' => 'access'];
    $token = auth('api')->claims($claims)->setTTL(120)->login($user);
    $data = ['access' => $token, 'expires_at' => now()->addHours(2)];

    if ($refresh) {
      $claims['type'] = 'refresh';
      $refresh = auth('api')->claims($claims)->setTTL(1440)->login($user);

      $data['refresh'] = $refresh;
    }

    return $data;
  }

  public static function refreshToken(bool $blacklist = false, bool $resetClaims = false)
  {
    return [
      'access' => auth('api')->setTTL(120)->claims(['type' => 'access'])->refresh($blacklist, $resetClaims),
      'expires_at' => now()->addHours(2),
    ];
  }

  public static function user(User $user, bool $relations = false, string $channel = UserChannelEnum::Tenant)
  {
    if ($channel == UserChannelEnum::Mobile) {
      $relations = false;
    }

    return AuthUserResource::make($user, $relations);
  }
}