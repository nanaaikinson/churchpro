<?php

namespace App\Actions\Central\Auth;

use App\Enums\UserProviderEnum;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Jobs\LogoutUserFromAllDevicesJob;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdatePassword
{
  use AsAction, ApiResponse;

  public function handle(UpdatePasswordRequest $request)
  {
    DB::beginTransaction();

    try {
      /**
       * @var \App\Models\User $user
       */
      $user = auth('api')->user();
      $user->load('passwords');
      $password = $request->input('new_password');

      // If user signed in using social media
      if ($user->sign_up_provider != UserProviderEnum::Local) {
        throw new \Exception('You are not allowed to update your password.');
      }

      // If password has been used before
      $user->passwords->each(function ($item) use ($password) {
        if (\Hash::check($password, $item->password)) {
          throw new \Exception('Password has been used before.');
        }
      });

      // Update password
      $hashedPassword = \Hash::make($password);
      $user->update(['password' => $hashedPassword]);

      // Save password to history
      $user->passwords()->create(['password' => $hashedPassword]);

      // Invalidate all tokens and logout from all devices
      auth('api')->logout(true);
      LogoutUserFromAllDevicesJob::dispatch($user)->afterCommit();

      DB::commit();

      return $this->messageResponse('Password successfully updated.');
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
