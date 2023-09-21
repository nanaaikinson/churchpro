<?php

namespace App\Actions\Central\Auth;

use App\Enums\FileUploadContentTypeEnum;
use App\Enums\UserChannelEnum;
use App\Enums\UserOnboardingStepEnum;
use App\Enums\UserProviderEnum;
use App\Helpers\AuthHelper;
use App\Models\User;
use App\Services\FileService;
use App\Traits\ApiResponse;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Laravel\Socialite\Facades\Socialite;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SocialSignUp
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
    DB::beginTransaction();
    $channel = $request->input('channel');

    try {
      $accessToken = $request->input('access_token');
      $result = Socialite::driver('google')->userFromToken($accessToken);
      $user = User::where('email', $result->user['email'])->first();

      if ($user) {
        if ($user->sign_up_provider == UserProviderEnum::Local) {
          throw new \Exception('An account with this email already exists. Please sign in with your email and password.');
        }

        if ($user->sign_up_provider == UserProviderEnum::Google) {
          // Login
          return SocialSignIn::run($request);
        }
      } else {
        // Create user
        $user = User::create([
          'first_name' => $result->user['given_name'],
          'last_name' => $result->user['family_name'],
          'email' => $result->user['email'],
          'email_verified_at' => now(),
          'onboarding_step' => UserOnboardingStepEnum::TenantOnboarding,
          'channels' => json_encode([UserChannelEnum::Tenant, UserChannelEnum::Mobile]),
          'providers' => json_encode([UserProviderEnum::Google]),
          'sign_up_provider' => UserProviderEnum::Google,
        ]);

        dispatch(function () use ($result, $user) {
          // Upload and assign avatar
          $image = Image::make($result->user['picture'])->encode('jpg');
          $media = FileService::uploadFromString($image, FileUploadContentTypeEnum::Avatar);

          $user->attachMedia($media, 'avatar');
        })->afterCommit();

        $data = AuthHelper::userWithToken($user, $channel, true);

        DB::commit();

        return $this->dataResponse($data);
      }
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
