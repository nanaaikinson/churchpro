<?php

use App\Enums\OrganizationApprovalEnum;
use App\Enums\OrganizationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('organizations', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->string('name');
      $table->string('domain')->nullable();
      $table->string('approval')->default(OrganizationApprovalEnum::Pending);
      $table->string('status')->default(OrganizationStatus::Disabled);
      $table->json('data')->nullable();
      $table->json('settings')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('organizations');
  }
};
