<?php

namespace App\Actions\Central\Auth;

use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class Logout
{
  use AsAction, ApiResponse;

  public function handle()
  {
    DB::beginTransaction();

    try {
      /**
       * @var \App\Models\User $user
       */
      $user = auth('api')->user();
      $payload = auth('api')->payload();

      // Invalidate token
      $reference = $payload->get('reference');
      $user->authTokens()->where('reference', $reference)->update(['whitelisted' => false]);

      // Logout user
      auth('api')->logout(true);

      DB::commit();

      return $this->messageResponse('Logout successful.');
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
