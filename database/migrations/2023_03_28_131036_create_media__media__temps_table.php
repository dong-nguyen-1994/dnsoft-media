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
        Schema::create('media__media_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('media_id')->index();
            $table->string('session_id');
            $table->enum('type', ['single', 'multiple']);
            $table->boolean('is_collection')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media__media_temps');
    }
};
