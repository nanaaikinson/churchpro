<?php

use App\Actions\Central\Auth\RegisterAction;
use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
  Route::post('register', RegisterAction::class);
});