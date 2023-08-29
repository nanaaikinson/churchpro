<?php

namespace App\Actions\Auth;

use App\Traits\ApiResponse;
use Laravel\Socialite\Facades\Socialite;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SocialSignIn
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
    return [
      'access_token' => ['required', 'string'],
    ];
  }

  public function handle(ActionRequest $request)
  {
    try {
      $accessToken = $request->input('access_token');
      $user = Socialite::driver('google')->userFromToken($accessToken);

      dd($user);

    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}