<?php

namespace App\Helpers;

use App\Enums\UserChannelEnum;
use App\Http\Resources\AuthUserResource;
use App\Models\User;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AuthHelper
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
    /**
     * @var JWTGuard $auth
     */
    $auth = auth('api');

    $reference = Str::random(32);
    $claims = ['channel' => $channel, 'type' => 'access', 'reference' => $reference];
    $token = $auth->claims($claims)->setTTL(120)->login($user);
    $data = ['access' => $token, 'expires_at' => now()->addHours(2)];

    // Create auth token
    $user->authTokens()->create(['reference' => $reference]);

    if ($refresh) {
      $claims['type'] = 'refresh';
      $refresh = $auth->claims($claims)->setTTL(1440)->login($user);

      $data['refresh'] = $refresh;
    }

    return $data;
  }

  public static function refreshToken(bool $blacklist = false, bool $resetClaims = false)
  {
    /**
     * @var JWTGuard $auth
     */
    $auth = auth('api');

    /**
     * @var User $user
     */
    $user = $auth->user();
    $reference = Str::random(32);

    $user->authTokens()->create(['reference' => $reference]);

    return [
      'access' => $auth->setTTL(120)->claims(['type' => 'access', 'reference' => $reference])->refresh($blacklist, $resetClaims),
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
