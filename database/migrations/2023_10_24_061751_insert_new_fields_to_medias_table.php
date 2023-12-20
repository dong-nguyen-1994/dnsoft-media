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
    Schema::table('medias', function (Blueprint $table) {
      $table->string('processed_file')->nullable();
      $table->enum('visibility', ['private', 'public', 'unslited'])->default('private');
      $table->boolean('processed')->default(false);
      $table->string('processing_percentage')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('elearning__courses', function (Blueprint $table) {
      Schema::dropColumns('processed_file');
      Schema::dropColumns('visibility');
      Schema::dropColumns('processed');
      Schema::dropColumns('processing_percentage');
    });
  }
};
