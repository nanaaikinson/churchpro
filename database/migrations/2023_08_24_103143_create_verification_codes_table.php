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
    Schema::create('verification_codes', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulidMorphs('verifiable');
      $table->string('code');
      $table->boolean('enabled')->default(false);
      $table->dateTime('expires_at');
      $table->timestamps();

      // Indexes
      $table->index(['verifiable_id', 'verifiable_type']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('verification_codes');
  }
};