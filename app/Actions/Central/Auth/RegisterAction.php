<?php

namespace App\Actions\Central\Auth;

use App\Enums\UserProviderEnum;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterAction
{
  use AsAction, ApiResponse;

  public function handle(RegisterRequest $request)
  {
    $request->validated();
    $channel = $request->input('channel');
    $provider = $request->input('provider');

    try {
      // Find user by email
      $user = User::where('email', $request->input('email'))->first();

      // Create user if not found
      if (!$user) {
        return $this->createNewUser($request, $channel, $provider);
      }

      // Check if user already registered with provider or channel
      if ($user->providers && in_array($provider, $user->providers)) {
        return $this->badRequestResponse(null, 'User already registered with this provider.');
      }

      // Check if user already registered with provider or channel
      if ($user->channels && in_array($channel, $user->channels)) {
        return $this->badRequestResponse(null, 'User already registered with this channel.');
      }

      // Update user if found
      $user->update([
        'channels' => json_encode(array_merge($user->channels, [$channel])),
        'providers' => json_encode(array_merge($user->providers, [$provider]))
      ]);

      return $this->messageResponse('Account created successfully.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }

  private function createNewUser(RegisterRequest $request, string $channel, string $provider)
  {
    // Persist user
    $user = User::create([
      'first_name' => $request->input('first_name'),
      'last_name' => $request->input('last_name'),
      'email' => $request->input('email'),
      'channels' => json_encode([$channel]),
      'providers' => json_encode([$provider]),
    ]);

    // Set password if local provider
    if ($provider == UserProviderEnum::Local) {
      $user->passwords()->create([
        'password' => bcrypt($request->input('password')),
      ]);
    }

    // TODO: Send verification email if local provider

    return $this->messageResponse('Account created successfully.');
  }
}