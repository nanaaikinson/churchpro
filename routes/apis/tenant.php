<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'tenant.resolver'])->group(function () {
  Route::prefix('prayers')->group(function () {
    Route::get('/', \App\Actions\Tenant\Prayer\Index::class);
    Route::get('/{prayer}', \App\Actions\Tenant\Prayer\Show::class);
  });

  Route::prefix('organization')->group(function () {
    Route::get('/', \App\Actions\Tenant\Organization\Show::class);
    Route::patch('/', \App\Actions\Tenant\Organization\Update::class);
  });
});