<?php

use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
  Route::post('local/sign-in', \App\Actions\Central\Auth\LocalSignIn::class);
  Route::post('local/sign-up', \App\Actions\Central\Auth\LocalSignUp::class);
  Route::post('social/sign-in', \App\Actions\Central\Auth\SocialSignIn::class);
  Route::post('social/sign-up', \App\Actions\Central\Auth\SocialSignUp::class);
});

/**
 * Protected routes
 */
Route::middleware('auth:api')->group(function () {
  // Auth
  Route::prefix('auth')->group(function () {
    Route::get('user', \App\Actions\Central\Auth\GetUser::class);
    Route::get('refresh-token', \App\Actions\Central\Auth\RefreshToken::class);
    Route::post('verify-account', \App\Actions\Central\Auth\VerifyAccount::class);
    Route::get('resend-verification', \App\Actions\Central\Auth\ResendEmailVerification::class);
    Route::patch('update-password', \App\Actions\Central\Auth\UpdatePassword::class);
    Route::patch('update-profile', \App\Actions\Central\Auth\UpdateProfile::class);
  });

  // Organization
  Route::post('organizations/onboard', \App\Actions\Central\Organization\Onboard::class);

  // Files
  Route::post('files/upload', \App\Actions\Central\Files\Upload::class);
});
