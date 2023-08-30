<?php

namespace App\Actions\Auth;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUser
{
  use AsAction, ApiResponse;

  public function handle(Request $request)
  {
    try {
      /**
       * @var \App\Models\User $user
       */
      $user = $request->user('api');
      $relations = $user->email_verified_at ? true : false;

      return $this->dataResponse(Helper::user($user, $relations), 'Successfully retrieved user.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}