<?php

namespace App\Actions\Central\Auth;

use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProfile
{
  use AsAction, ApiResponse;

  public function handle(UpdateProfileRequest $request)
  {
    DB::beginTransaction();

    try {
      /**
       * @var \App\Models\User $user
       */
      $user = auth('api')->user();

      $firstName = $request->input('first_name');
      $lastName = $request->input('last_name');
      $username = $request->input('username');
      $avatar = $request->input('avatar');

      $user->update([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'username' => $username,
      ]);

      if ($avatar) {
        $user->syncMedia($avatar, 'avatar');
      }

      DB::commit();

      return $this->messageResponse('Profile successfully updated.');
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse("", $e->getMessage());
    }
  }
}
