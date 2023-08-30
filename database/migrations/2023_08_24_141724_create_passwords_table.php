<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('passwords', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulid('user_id');
      $table->string('password');
      $table->timestamps();
      $table->softDeletes();

      // Foreign keys
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

      // Indexes
      $table->index(['deleted_at', 'user_id']);
    });
  }
};
