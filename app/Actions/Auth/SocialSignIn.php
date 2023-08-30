<?php

namespace App\Actions\Auth;

use App\Enums\UserStatusEnum;
use App\Models\User;
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
      $result = Socialite::driver('google')->userFromToken($accessToken);
      $user = User::where('email', $result->user['email'])->first();

      if (!$user) {
        throw new \Exception('Sorry. An account with this email does not exist.');
      }

      // Check status
      if ($user->status == UserStatusEnum::Active) {
        $data = Helper::userWithToken(user: $user, channel: 'local', refresh: true, relations: true);

        return $this->dataResponse($data, 'Successfully signed in.');
      }

      throw new \Exception('Your account has been suspended. Please contact support.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
