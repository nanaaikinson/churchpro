<?php

namespace App\Actions\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\AccountVerificationMail;
use App\Models\User;
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

      // Send user email verification
      dispatch(function () use ($user) {
        Mail::to($user->email)->send(new AccountVerificationMail($user));
      })->afterCommit();

      DB::commit();
      return $this->messageResponse('Thank you for signing up!. Your account has been created and If you confirm your account after you can now sign in with your credentials.');
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
