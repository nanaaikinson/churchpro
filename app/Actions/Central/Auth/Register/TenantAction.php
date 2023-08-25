<?php

namespace App\Actions\Central\Auth\Register;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\Register;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use DB;

class TenantAction
{
  use AsAction, ApiResponse;

  public function handle(RegisterRequest $request)
  {
    $channel = $request->input('channel');
    $provider = $request->input('provider');

    DB::beginTransaction();

    try {
      if ($channel !== UserChannelEnum::Tenant || $provider !== UserProviderEnum::Local) {
        throw new \Exception('You\'re forbidden from accessing this resource.');
      }

      // Find user by email
      $user = User::with('iams')->where('email', $request->input('email'))->first();

      // Create user if not found
      if (!$user) {
        $user = Register::make($request, $channel, $provider);
      } else {
        // Check if user has registered with channel and root IAM
        if ($user->channels && in_array($channel, $user->channels) && $user->iams->where('root', true)->first()) {
          return $this->badRequestResponse(null, 'You already have an account. Please login to continue.');
        }

        // Update user if found
        $user->update([
          'channels' => json_encode(array_merge($user->channels, [$channel])),
        ]);

        // Create IAM for user
        $user->iams()->create([
          'value' => generate_iam(),
          'root' => true,
        ]);
      }

      DB::commit();
      return $this->dataResponse(['id' => $user->id], 'Account created successfully.');

    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}