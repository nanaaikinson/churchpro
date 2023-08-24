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
    Schema::create('user_organizations', function (Blueprint $table) {
      $table->ulid('user_id');
      $table->ulid('organization_id');

      // Foreign keys
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');

      // Primary keys
      $table->primary(['user_id', 'organization_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('user_organizations');
  }
};