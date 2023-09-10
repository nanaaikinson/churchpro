<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('user_devices', function (Blueprint $table) {
      $table->string('type')->after('fcm_token');
      $table->softDeletes()->after('updated_at');

      // Indexes
      $table->index('type');
      $table->index('deleted_at');
    });
  }
};
