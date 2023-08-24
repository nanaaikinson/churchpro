<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('iams', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->ulid('user_id');
      $table->ulid('organization_id')->nullable();
      $table->string('value');
      $table->boolean('root')->default(false);
      $table->timestamps();
      $table->softDeletes();

      // Foreign keys
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');

      // Indexes
      $table->index(['deleted_at', 'user_id', 'organization_id', 'value']);
    });
  }
};