<?php

namespace App\Actions\Auth;

use App\Enums\UserOnboardingStepEnum;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyAccount
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
    return [
      'verification_code' => ['required', 'string']
    ];
  }

  public function handle(ActionRequest $request)
  {
    DB::beginTransaction();

    /**
     * @var User $user
     * @var \App\Models\VerificationCode $verificationCode
     */
    $user = $request->user('api');
    $code = $request->input('verification_code');

    try {
      $verificationCode = $user->verificationCodes()->where(['enabled' => true, 'code' => $code])->first();

      if ($user->email_verified_at || !$verificationCode || $verificationCode->expires_at->isPast()) {
        throw new \Exception('Verification code is invalid or expired.');
      }

      // Verify user email
      $user->update(['email_verified_at' => now(), 'onboarding_step' => UserOnboardingStepEnum::TenantOnboarding]);

      // Disable verification code
      $verificationCode->update(['enabled' => false]);

      // TODO: Send welcome and account verified email
      dispatch(function () { })->afterCommit();

      DB::commit();

      return $this->messageResponse('Your account has been verified successfully.');
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}