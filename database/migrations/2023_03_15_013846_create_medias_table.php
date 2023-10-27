<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediasTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('medias', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name');
      $table->string('file_name');
      $table->string('disk');
      $table->string('mime_type');
      $table->unsignedBigInteger('size');
      $table->nullableMorphs('author');
      $table->unsignedBigInteger('folder_id')->nullable();
      $table->string('alt')->nullable();
      $table->string('duration', 15)->nullable();

      $table->foreign('folder_id')->references('id')->on('media__folders')->onDelete('CASCADE');
      $table->string('processed_file')->nullable();
      $table->enum('visibility', ['private', 'public', 'unslited'])->default('private');
      $table->boolean('processed')->default(false);
      $table->string('processing_percentage')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('medias');
  }
}
