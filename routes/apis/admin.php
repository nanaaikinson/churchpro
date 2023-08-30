<?php

use Illuminate\Support\Facades\Route;

Route::prefix('organizations')->group(function () {
  Route::patch('approval', \App\Actions\Admin\OrganizationApproval::class);
});