<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('comments', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulidMorphs('commentable');
      $table->ulid('user_id');
      $table->text('body');
      $table->timestamps();
      $table->softDeletes();

      // Foreign keys
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

      // Indexes
      $table->index(['commentable_id', 'commentable_type', 'deleted_at']);
    });
  }
};