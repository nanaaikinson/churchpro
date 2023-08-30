<?php

use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
  Route::post('local/sign-in', \App\Actions\Auth\LocalSignIn::class);
  Route::post('local/sign-up', \App\Actions\Auth\LocalSignUp::class);
  Route::post('social/sign-in', \App\Actions\Auth\SocialSignIn::class);
  Route::post('social/sign-up', \App\Actions\Auth\SocialSignUp::class);
});

/**
 * Protected routes
 */
Route::middleware('auth:api')->group(function () {
  Route::prefix('auth')->group(function () {
    Route::get('user', \App\Actions\Auth\GetUser::class);
    Route::post('verify-account', \App\Actions\Auth\VerifyAccount::class);
    Route::post('resend-verification', \App\Actions\Auth\ResendEmailVerification::class);
  });

  Route::post('onboard-organization', \App\Actions\Organization\Onboard::class);
});