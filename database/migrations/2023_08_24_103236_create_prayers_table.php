<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('prayers', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulid('organization_id');
      $table->ulid('user_id');
      $table->string('title');
      $table->text('description')->nullable();
      $table->timestamps();
      $table->softDeletes();

      // Foreign keys
      $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

      // Indexes
      $table->index(['user_id', 'organization_id', 'deleted_at']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('prayers');
  }
};