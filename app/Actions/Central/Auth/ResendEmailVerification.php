<?php

namespace App\Actions\Central\Auth;

use App\Mail\AccountVerificationMail;
use App\Models\User;
use App\Models\VerificationCode;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class ResendEmailVerification
{
  use AsAction, ApiResponse;

  public function handle(): JsonResponse
  {
    DB::beginTransaction();

    try {
      $user = auth('api')->user();

      // If user has already verified their email, return error
      if ($user->email_verified_at) {
        return $this->badRequestResponse(null, 'Your account has already been verified.');
      }

      // If user has an existing verification code, disable it
      if ($user->verificationCodes->isNotEmpty()) {
        $user->verificationCodes()->update(['enabled' => false]);
      }

      // Create new verification code
      $code = $user->verificationCodes()->create([
        'code' => generate_verification_code(8),
        'enabled' => true,
        'expires_at' => now()->addDay(),
      ]);

      // Send user email verification
      dispatch(function () use ($user, $code) {
        Mail::to($user->email)->send(new AccountVerificationMail($user, $code->code, true));
      })->afterCommit();

      DB::commit();

      return $this->messageResponse('A verification code has been sent to your email.');

    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}