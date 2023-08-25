<?php

use App\Actions\Central\Auth\LoginAction;
use App\Actions\Central\Auth\Register\TenantAction;
use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
  Route::post('login', LoginAction::class);
  Route::post('register', TenantAction::class);
});