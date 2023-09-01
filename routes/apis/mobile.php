<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
  Route::prefix('prayers')->group(function () {
    Route::get('/', \App\Actions\Mobile\Prayers\Index::class);
    Route::post('/', \App\Actions\Mobile\Prayers\Store::class);
  });
});