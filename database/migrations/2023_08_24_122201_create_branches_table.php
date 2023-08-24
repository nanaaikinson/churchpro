<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('branches', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulid('organization_id');
      $table->string('name');
      $table->string('slug');
      $table->text('description')->nullable();
      $table->json('address')->nullable();
      $table->json('contact')->nullable();

      $table->timestamps();
      $table->softDeletes();

      // Foreign keys
      $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');

      // Indexes
      $table->index(['organization_id', 'deleted_at']);
    });
  }
};