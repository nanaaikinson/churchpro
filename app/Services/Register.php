<?php

namespace App\Services;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use App\Http\Requests\RegisterRequest;
use App\Mail\AccountVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class Register
{
  public static function make(RegisterRequest $request, string $channel, string $provider)
  {
    // Persist user
    $user = User::create([
      'first_name' => $request->input('first_name'),
      'last_name' => $request->input('last_name'),
      'email' => $request->input('email'),
      'channels' => json_encode([$channel]),
      'providers' => json_encode([$provider]),
    ]);

    // Set password if local provider
    if ($provider == UserProviderEnum::Local) {
      $user->passwords()->create([
        'password' => bcrypt($request->input('password')),
      ]);
    }

    if ($channel == UserChannelEnum::Tenant) {
      $iam = $user->iams()->create([
        'value' => generate_iam(),
        'root' => true,
      ]);
    }

    // Send verification email if local provider
    dispatch(function () use ($user) {
      Mail::to($user->email)->send(new AccountVerificationMail($user));
    });

    return $user;
  }
}