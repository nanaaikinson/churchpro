<?php

use Illuminate\Support\Facades\Route;

Route::prefix('prayers')->middleware(['auth:api', 'tenant.resolver'])->group(function () {
  Route::get('/', \App\Actions\Tenant\Prayer\Index::class);
});