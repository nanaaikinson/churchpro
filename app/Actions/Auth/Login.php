<?php

namespace App\Actions\Auth;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class Login
{
  use AsAction, ApiResponse;

  public function handle(LoginRequest $request)
  {
    try {
      $email = $request->input('email');
      $password = $request->input('password');
      $provider = $request->input('provider');
      $channel = $request->input('channel');
      $accessToken = $request->input('google_access_token');

      $user = User::with(['passwords' => fn($query) => $query->where('active', true)])->withMedia('avatar')->where('email', $email)->first();

      // If no user found, return error
      if (!$user) {
        throw new \Exception('Invalid credentials provided.');
      }

      if ($provider == UserProviderEnum::Local) {
        return $this->local($user, $channel, $password);
      } else {
        return $this->social($user, $channel, $accessToken);
      }

    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }

  private function local(User $user, string $channel, string $password)
  {
    // Admin
    if ($channel == UserChannelEnum::Admin && !in_array(UserChannelEnum::Admin, $user->channels)) {
      return $this->badRequestResponse(null, 'Invalid credentials provided.');
    }

    if (!in_array(UserProviderEnum::Local, $user->providers)) {
      return $this->badRequestResponse(null, 'Invalid credentials provided.');
    }

    if ($user->passwords->isEmpty() || !Hash::check($password, $user->passwords->first()->password)) {
      return $this->badRequestResponse(null, 'Invalid credentials provided.');
    }

  }

  private function social(User $user, string $channel, string $accessToken)
  {
  }

  public static function withToken(User $user, string $channel, bool $refresh = false, string $onboardingStep = ''): array
  {
    $claims = ['channel' => $channel, 'onboarding_step' => $onboardingStep];

    $token = auth('api')
      ->claims($claims)
      // ->setTTL(config('chysnc.jwt.ttl.access'))
      ->login($user);

    $data = ['access_token' => $token, 'expires_at' => now()->addDay()];

    if ($refresh) {
      $refresh = auth('api')
        ->claims($claims)
        // ->setTTL(config('chysnc.jwt.ttl.refresh'))
        ->login($user);

      $data['refresh_token'] = $refresh;
    }

    return $data;
  }
}