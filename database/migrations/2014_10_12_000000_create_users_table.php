<?php

use App\Enums\UserStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('users', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->string('first_name');
      $table->string('last_name');
      $table->string('email')->unique();
      $table->string('password')->nullable();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('status')->default(UserStatusEnum::Active);
      $table->string('onboarding_step')->default(\App\Enums\UserOnboardingStepEnum::AccountCreation);
      $table->boolean('two_step')->default(false);
      $table->json('channels')->nullable();
      $table->json('providers')->nullable();
      $table->rememberToken();
      $table->timestamps();
      $table->softDeletes();

      // Indexes
      $table->index(['deleted_at']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('users');
  }
};