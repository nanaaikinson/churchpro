<?php

namespace App\Actions\Auth;

use App\Enums\FileUploadContentTypeEnum;
use App\Enums\UserChannelEnum;
use App\Enums\UserOnboardingStepEnum;
use App\Enums\UserProviderEnum;
use App\Models\User;
use App\Services\FileService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Laravel\Socialite\Facades\Socialite;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Plank\Mediable\Facades\MediaUploader;

class SocialSignUp
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
    DB::beginTransaction();

    try {
      $accessToken = $request->input('access_token');
      $result = Socialite::driver('google')->userFromToken($accessToken);

      // Create user
      $user = User::create([
        'first_name' => $result->user['given_name'],
        'last_name' => $result->user['family_name'],
        'email' => $result->user['email'],
        'email_verified_at' => now(),
        'onboarding_step' => UserOnboardingStepEnum::TenantOnboarding,
        'channels' => json_encode([UserChannelEnum::Tenant, UserChannelEnum::Mobile]),
        'providers' => json_encode([UserProviderEnum::Google]),
      ]);

      dispatch(function () use ($result, $user) {
        // Upload and assign avatar
        $image = Image::make($result->user['picture'])->encode('jpg');
        $media = FileService::uploadFromString($image, FileUploadContentTypeEnum::Avatar);

        $user->attachMedia($media, 'avatar');
      })->afterCommit();

      // Get tokens
      $tokens = Login::withToken(
        user: $user,
        channel: UserChannelEnum::Tenant,
        refresh: true,
        onboardingStep: UserOnboardingStepEnum::TenantOnboarding
      );

      DB::commit();

      return $this->dataResponse(array_merge([
        'user' => [
          'first_name' => $user->first_name,
          'last_name' => $user->last_name,
          'avatar' => $result->user['picture']
        ]
      ], ['tokens' => $tokens]));

    } catch (\Exception $e) {
      DB::rollBack();

      \Log::error($e);

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}