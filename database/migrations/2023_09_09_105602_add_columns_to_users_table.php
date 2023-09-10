<?php

use App\Enums\UserProviderEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->after('email', function () use ($table) {
        $table->string('username')->unique()->nullable();
        // Signup type: local, google, facebook, etc.
        $table->string('sign_up_provider')->default(UserProviderEnum::Local);
      });
    });
  }
};
