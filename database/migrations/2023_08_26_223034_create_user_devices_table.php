<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('user_devices', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulid('user_id');
      $table->string('device_id');
      $table->string('fcm_token');
      $table->boolean('active')->default(false);
      $table->timestamps();

      // Foreign keys
      $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

      // Indexes
      $table->index('user_id');
    });
  }
};
