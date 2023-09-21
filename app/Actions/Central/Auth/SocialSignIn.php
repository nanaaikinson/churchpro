<?php

namespace App\Actions\Central\Auth;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use App\Enums\UserStatusEnum;
use App\Helpers\AuthHelper;
use App\Models\User;
use App\Traits\ApiResponse;
use BenSampo\Enum\Rules\EnumValue;
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
      'channel' => ['required', 'string', new EnumValue(UserChannelEnum::class)]
    ];
  }

  public function handle(ActionRequest $request)
  {
    $channel = $request->input('channel');

    try {
      $accessToken = $request->input('access_token');
      $result = Socialite::driver('google')->userFromToken($accessToken);
      $user = User::where('email', $result->user['email'])->first();

      if (!$user) {
        return SocialSignUp::run($request);
      }

      if ($user->sign_up_provider == UserProviderEnum::Local) {
        throw new \Exception('This account was registered with an email and password. Please sign in with that instead.');
      }

      // Check status
      if ($user->status == UserStatusEnum::Active) {
        $data = AuthHelper::userWithToken(user: $user, channel: $channel, refresh: true, relations: true);

        return $this->dataResponse($data, 'Successfully signed in.');
      }

      throw new \Exception('Your account has been suspended. Please contact support.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
