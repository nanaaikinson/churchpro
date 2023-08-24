<?php

namespace App\Actions\Central\Auth;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use App\Enums\UserStatusEnum;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AdminUserResource;
use App\Http\Resources\MobileUserResource;
use App\Http\Resources\TenantUserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class LoginAction
{
  use AsAction, ApiResponse;

  private int $accessTokenTtl;
  private int $refreshTokenTtl;

  public function __construct()
  {
    $this->accessTokenTtl = (int) config('chsyc.jwt.ttl.access');
    $this->refreshTokenTtl = (int) config('chsyc.jwt.ttl.refresh');
  }

  public function handle(LoginRequest $request): JsonResponse
  {
    $channel = $request->input('channel');

    try {
      // Find user by email
      $user = User::with(['iams', 'passwords' => fn($query) => $query->whereActive(true)])
        ->withMedia('avatar')
        ->where('email', $request->input('email'))
        ->first();

      // Check if user found
      if (!$user) {
        throw new \Exception('Incorrect credentials provided.');
      }

      // Check if user has verified email
      if (!$user->email_verified_at) {
        // TODO: Send verification email
        throw new \Exception('Your account has not been verified. Please check your email for the verification link.');
      }

      // Check for user status
      if ($user->status == UserStatusEnum::Suspended) {
        throw new \Exception('Your account has been suspended. Please contact support for more information.');
      }

      // Mobile channel
      if ($channel == UserChannelEnum::Mobile) {
        return $this->mobileLogin($request, $user);
      }

      // Tenant channel
      if ($channel == UserChannelEnum::Tenant) {
        return $this->tenantLogin($request, $user);
      }

      // Admin channel
      return $this->adminLogin($request, $user);
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }

  private function adminLogin(LoginRequest $request, User $user): JsonResponse
  {
    $pwdCheck = Hash::check($request->input('password'), $user->passwords->first()->password);

    if ($user->channels && in_array(UserChannelEnum::Admin, $user->channels) && $pwdCheck) {
      return $this->dataResponse(
        array_merge(
          $this->withToken($user, UserChannelEnum::Admin),
          ['user' => AdminUserResource::make($user)]
        )
      );
    }

    return $this->badRequestResponse(null, 'Incorrect credentials provided.');
  }

  private function mobileLogin(LoginRequest $request, User $user): JsonResponse
  {
    $provider = $request->input('provider');
    $pwdCheck = Hash::check($request->input('password'), $user->passwords->first()->password);

    // Check if channel is mobile and user has mobile channel
    if ($user->channels && in_array(UserChannelEnum::Mobile, $user->channels)) {
      // Check if user has provided provider
      if (!$user->providers->contains($provider)) {
        return $this->badRequestResponse(null, 'Incorrect credentials provided.');
      }

      // Local provider
      if ($provider == UserProviderEnum::Local && $pwdCheck) {
        return $this->dataResponse(
          array_merge(
            $this->withToken($user, UserChannelEnum::Mobile),
            ['user' => MobileUserResource::make($user)]
          )
        );
      }

      // Social provider
      if ($provider == UserProviderEnum::Google) {
        return $this->dataResponse(
          array_merge(
            $this->withToken($user, UserChannelEnum::Mobile),
            ['user' => MobileUserResource::make($user)]
          )
        );
      }
    }

    return $this->badRequestResponse(null, 'Incorrect credentials provided.');
  }

  private function tenantLogin(LoginRequest $request, User $user): JsonResponse
  {
    $pwdCheck = Hash::check($request->input('password'), $user->passwords->first()->password);
    $iam = $request->input('iam');

    if (
      $user->channels
      && in_array(UserChannelEnum::Tenant, $user->channels)
      && $pwdCheck
      && $user->iams->isNotEmpty()
      && $user->iams->contains('value', $iam)
    ) {
      return $this->dataResponse(
        array_merge(
          $this->withToken($user, UserChannelEnum::Tenant),
          ['user' => TenantUserResource::make($user)]
        )
      );
    }

    return $this->badRequestResponse(null, 'Incorrect credentials provided.');
  }

  private function withToken(User $user, string $channel): array
  {
    $token = auth('api')
      ->setTTL($this->accessTokenTtl)
      ->claims(['channel' => $channel])
      ->login($user);

    $refreshToken = auth('api')
      ->setTTL($this->refreshTokenTtl)
      ->claims(['channel' => $channel])
      ->login($user);

    $expiresIn = now()->addHours(2)->toDateTimeString();

    return [
      'token' => [
        'access_token' => $token,
        'token_type' => 'bearer',
        'refresh_token' => $refreshToken,
        'expires_in' => $expiresIn
      ]
    ];
  }
}