<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('bookmarks', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulidMorphs('bookmarkable');
      $table->ulid('user_id');
      $table->timestamps();

      // Foreign keys
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

      // Indexes
      $table->index(['bookmarkable_id', 'bookmarkable_type']);
      $table->index(['user_id']);
    });
  }
};