<?php

namespace App\Actions\Auth;

use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class LocalSignIn
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
    return [
      'email' => ['required', 'email'],
      'password' => ['required', 'string']
    ];
  }

  public function handle(ActionRequest $request)
  {
    try {
      $user = User::where('email', $request->input('email'))->first();

      if ($user && Hash::check($request->input('password'), $user->password)) {

        // Check if user is verified
        if (!$user->email_verified_at) {
          $data = Helper::userWithToken(user: $user, channel: 'local', refresh: true, relations: false);

          return $this->dataResponse($data, 'Please verify your account.');
        }

        // Check status
        if ($user->status == UserStatusEnum::Active) {
          $data = Helper::userWithToken(user: $user, channel: 'local', refresh: true, relations: true);

          return $this->dataResponse($data, 'Successfully signed in.');
        }

        throw new \Exception('Your account has been suspended. Please contact support.');
      }
      throw new \Exception('Invalid credentials provided.');

    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}