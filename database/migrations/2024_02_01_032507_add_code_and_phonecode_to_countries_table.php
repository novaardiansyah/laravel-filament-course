<?php

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
    Schema::table('countries', function (Blueprint $table) {
      $table->char('code', 2)->after('name')->nullable();
      $table->char('phonecode', 5)->after('code')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('countries', function (Blueprint $table) {
      $table->dropColumn('code');
      $table->dropColumn('phonecode');
    });
  }
};
