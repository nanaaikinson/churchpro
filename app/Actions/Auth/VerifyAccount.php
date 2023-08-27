<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyAccount
{
  use AsAction, ApiResponse;

  public function rules(): array {
    return [
      'email' => ['required', 'string', 'email', 'max:191'],
      'verification_code' => ['required', 'string']
    ];
  }

  public function handle(ActionRequest $request)
  {
    DB::beginTransaction();

    $email = $request->input('email');
    $code = $request->input('verification_code');

    try {
      $user = User::with('verificationCodes')->where('email', $email)->first();
      $verificationCode = $user->verificationCodes->where(['enabled' => true, 'code' => $code])->first();

      if ( !$user || $user->verificationCodes->isEmpty() || $user->email_verified_at
        || !$verificationCode || $verificationCode->expires_at->isPast()
      ) {
        throw new \Exception('Verification code is invalid.');
      }

      // Verify user email
      $user->update([
        'email_verified_at' => now()
      ]);

      // Disable verification code
      $verificationCode->update([
        'enabled' => false
      ]);

      DB::commit();

      return $this->messageResponse('Your account has been verified successfully.');
    }
    catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
