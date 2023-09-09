<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'auth.token.validity', 'tenant.resolver'])->group(function () {
  Route::prefix('prayers')->group(function () {
    Route::get('/', \App\Actions\Tenant\Prayer\Index::class);
    Route::get('/{prayer}', \App\Actions\Tenant\Prayer\Show::class);
  });

  Route::prefix('organization')->group(function () {
    Route::get('/', \App\Actions\Tenant\Organization\Show::class);
    Route::patch('/', \App\Actions\Tenant\Organization\Update::class);
  });

  Route::prefix('events')->group(function () {
    Route::get('/', \App\Actions\Tenant\Events\Index::class);
    Route::post('/', \App\Actions\Tenant\Events\Store::class);
    Route::get('/{event}', \App\Actions\Tenant\Events\Show::class);
    Route::patch('/{event}', \App\Actions\Tenant\Events\Update::class);
    Route::delete('/{event}', \App\Actions\Tenant\Events\Delete::class);
  });
});
