<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('user_auth_tokens', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulid('user_id');
      $table->string('reference')->unique();
      $table->boolean('whitelisted')->default(true);
      $table->timestamps();

      // Foreign keys
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

      // Indexes
      $table->index('user_id');
    });
  }
};
