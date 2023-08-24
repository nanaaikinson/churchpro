<?php

use Illuminate\Support\Facades\Route;

Route::prefix('c')->group(base_path('routes/apis/central.php'));
Route::prefix('t')->group(base_path('routes/apis/tenant.php'));
Route::prefix('m')->group(base_path('routes/apis/mobile.php'));
Route::prefix('a')->group(base_path('routes/apis/admin.php'));