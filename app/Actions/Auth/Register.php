<?php

namespace App\Actions\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\AccountVerificationMail;
use App\Models\User;
use App\Models\VerificationCode;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class Register
{
  use AsAction, ApiResponse;

  public function handle(RegisterRequest $request)
  {
    DB::beginTransaction();

    try {
      $user = User::create([
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'email' => $request->input('email'),
      ]);

      /**
       * @var VerificationCode $verificationCode
       */
      // Create email verification code
      $verificationCode = $user->verificationCodes()->create([
        'code' => generate_verification_code(8),
        'enabled' => true,
        'expires_at' => now()->addDay(),
      ]);

      // Send user email verification
      dispatch(function () use ($user, $verificationCode) {
        Mail::to($user->email)->send(new AccountVerificationMail($user, $verificationCode));
      })->afterCommit();

      DB::commit();
      $appName = config('app.name');

      return $this->dataResponse(
        ['id' => $user->id],
        "Thank you for signing up! We're thrilled to have you join {$appName}.
        Your account creation is almost complete. Please check your email for a verification code.
        Once you've received it, simply enter the code in the input below to finalize your account setup.
        We can't wait to see you make the most of our platform!"
      );
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
