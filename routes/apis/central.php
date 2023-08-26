<?php

use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
  Route::post('register', \App\Actions\Auth\Register::class);
  Route::post('resend-email-verification', \App\Actions\Auth\ResendEmailVerification::class);
});
