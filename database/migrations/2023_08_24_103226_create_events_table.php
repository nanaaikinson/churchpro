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
    Schema::create('events', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulid('organization_id');
      $table->string('title');
      $table->text('description')->nullable();
      $table->dateTime('start_date');
      $table->dateTime('end_date');
      $table->string('location')->nullable();
      $table->boolean('published')->default(false);
      $table->timestamps();
      $table->softDeletes();

      // Foreign Keys
      $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');

      // Indexes
      $table->index(['organization_id', 'deleted_at']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('events');
  }
};