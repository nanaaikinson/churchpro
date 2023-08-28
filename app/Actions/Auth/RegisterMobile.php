<?php

namespace App\Actions\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterMobile
{
  use AsAction, ApiResponse;

  public function handle(RegisterRequest $request)
  {
    DB::beginTransaction();

    try {
      $user = User::where('email', $request->input('email'))->first();
    }
    catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
