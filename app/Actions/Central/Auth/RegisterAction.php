<?php

namespace App\Actions\Central\Auth;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use App\Http\Requests\RegisterRequest;
use App\Mail\AccountVerificationMail;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;
use DB;

class RegisterAction
{
  use AsAction, ApiResponse;

  public function handle(RegisterRequest $request)
  {
    $channel = $request->input('channel');
    $provider = $request->input('provider');

    DB::beginTransaction();

    try {
      // Find user by email
      $user = User::where('email', $request->input('email'))->first();

      // Create user if not found
      if (!$user) {
        $user = $this->createNewUser($request, $channel, $provider);
      } else {
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
      }

      DB::commit();
      return $this->dataResponse(['id' => $user->id], 'Account created successfully.');
    } catch (\Exception $e) {
      DB::rollBack();

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

    if ($channel == UserChannelEnum::Tenant) {
      $iam = $user->iams()->create([
        'value' => generate_iam(),
        'root' => true,
      ]);
    }

    // TODO: Send verification email if local provider
    dispatch(function () use ($user) {
      Mail::to($user->email)->send(new AccountVerificationMail($user));
    })->afterCommit();

    return $user;
  }
}