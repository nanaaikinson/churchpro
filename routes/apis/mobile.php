<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'auth.token.validity'])->group(function () {
  Route::prefix('prayers')->group(function () {
    Route::get('/', \App\Actions\Mobile\Prayers\Index::class);
    Route::post('/', \App\Actions\Mobile\Prayers\Store::class);
  });

  Route::prefix('organizations')->group(function () {
    Route::get('/', \App\Actions\Mobile\Organizations\Index::class);
    Route::post('/bookmark', \App\Actions\Mobile\Organizations\Bookmark::class);
  });
});
