<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertFieldsMediaFileManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media__file_manager', function (Blueprint $table) {
            $table->string('origin_path')->nullable();
            $table->string('thumbnail_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media__file_manager', function (Blueprint $table) {
            $table->dropColumn('origin_path');
            $table->dropColumn('thumbnail_path');
        });
    }
}
