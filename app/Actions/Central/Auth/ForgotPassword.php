<?php

namespace App\Actions\Central\Auth;

use App\Enums\UserProviderEnum;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ForgotPassword
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
    return [
      'email' => ['required', 'email'],
    ];
  }

  public function handle(ActionRequest $request)
  {
    try {
      $user = User::where('email', $request->input('email'))->first();

      if ($user && $user->sign_up_provider == UserProviderEnum::Local) {
        dispatch(function () use ($user) {
          Mail::to($user->email)->send(new PasswordResetMail($user));
        });

        return $this->messageResponse('Password reset link sent.');
      }

      return $this->messageResponse('Password reset link sent.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
