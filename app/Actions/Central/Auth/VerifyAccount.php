<?php

namespace App\Actions\Central\Auth;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyAccount
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
    return [
      'verification_code' => ['required', 'string'],
      'channel' => ['required', Rule::in([UserChannelEnum::Web, UserChannelEnum::Mobile])],
    ];
  }

  public function handle(ActionRequest $request)
  {
    DB::beginTransaction();

    /**
     * @var \App\Models\VerificationCode $verificationCode
     * @var \App\Models\User $user
     */
    $user = auth('api')->user();
    $code = $request->input('verification_code');
    $channel = $request->input('channel');

    try {
      if (!in_array($user->sign_up_provider, [UserProviderEnum::Google, UserProviderEnum::Apple]) || $user->email_verified_at) {
        $verificationCode = $user->verificationCodes()->where(['enabled' => true, 'code' => $code])->first();

        if (!$verificationCode || $verificationCode->expires_at->isPast()) {
          throw new \Exception('Verification code is invalid or expired.');
        }

        // Verify user email
        $user->update(['email_verified_at' => now()]);

        // Disable verification code
        $verificationCode->update(['enabled' => false]);

        // TODO: Send welcome and account verified email
        // dispatch(function () {
        // })->afterCommit();

        DB::commit();
      }

      return $this->messageResponse('Your account has been verified successfully.');
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
