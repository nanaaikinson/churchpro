<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('likes', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulidMorphs('likeable');
      $table->ulid('user_id');
      $table->timestamps();

      // Foreign keys
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

      // Indexes
      $table->index(['likeable_id', 'likeable_type']);
      $table->index(['user_id']);
    });
  }
};