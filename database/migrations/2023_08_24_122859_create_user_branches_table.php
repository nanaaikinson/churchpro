<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('user_branches', function (Blueprint $table) {
      $table->ulid('user_id');
      $table->ulid('branch_id');

      // Foreign keys
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');

      // Primary key
      $table->primary(['user_id', 'branch_id']);
    });
  }
};