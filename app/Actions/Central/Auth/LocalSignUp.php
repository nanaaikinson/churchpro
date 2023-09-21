<?php

namespace App\Actions\Central\Auth;

use App\Enums\UserChannelEnum;
use App\Enums\UserOnboardingStepEnum;
use App\Enums\UserProviderEnum;
use App\Helpers\AuthHelper;
use App\Mail\AccountVerificationMail;
use App\Models\User;
use App\Traits\ApiResponse;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class LocalSignUp
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
    $rule = Password::min(8)->letters()->numbers()->mixedCase()->symbols();

    return [
      'first_name' => ['required', 'string', 'max:191'],
      'last_name' => ['required', 'string', 'max:191'],
      'email' => ['required', 'email', Rule::unique('users', 'email')],
      'password' => ['required', 'string', 'max:50', in_production() ? $rule->uncompromised() : $rule],
      'channel' => ['required', 'string', new EnumValue(UserChannelEnum::class)]
    ];
  }

  public function handle(ActionRequest $request)
  {
    DB::beginTransaction();
    $appName = config('app.name');
    $channel = $request->input('channel');

    try {
      // Create user
      $password = bcrypt($request->input('password'));

      $user = User::create([
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'email' => $request->input('email'),
        'password' => $password,
        'onboarding_step' => UserOnboardingStepEnum::AccountVerification,
        'channels' => json_encode([UserChannelEnum::Tenant, UserChannelEnum::Mobile]),
        'providers' => json_encode([UserProviderEnum::Local]),
        'sign_up_provider' => UserProviderEnum::Local,
      ]);

      // Create user password
      $user->passwords()->create([
        'password' => $password
      ]);

      // Send user account verification
      $code = generate_verification_code(8);
      $user->verificationCodes()->create([
        'code' => $code,
        'enabled' => true,
        'expires_at' => now()->addDay()
      ]);

      dispatch(function () use ($code, $user) {
        Mail::to($user->email)->send(new AccountVerificationMail($user, $code, false));
      })->afterCommit();

      $data = AuthHelper::userWithToken($user, $channel, true);

      DB::commit();

      return $this->dataResponse($data, "Thank you for signing up! We're thrilled to have you join {$appName}.
        Your account creation is almost complete. Please check your email for a verification code.
        Once you've received it, simply enter the code in the verification code input to finalize your account setup.");
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
