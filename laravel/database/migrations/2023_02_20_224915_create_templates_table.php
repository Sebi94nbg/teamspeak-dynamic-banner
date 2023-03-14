<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('alias');
            $table->string('filename');
            $table->string('file_path_original')->comment('The original file, uploaded by the user.');
            $table->string('file_path_drawed_grid')->comment('The original file, uploaded by the user, but with a grid system on it.');
            $table->string('file_path_drawed_grid_text')->comment('The uploaded file by the user, but with a grid system and the configured texts on it.');
            $table->string('file_path_drawed_text')->comment('The uploaded file by the user, but with the configured texts on it.');
            $table->integer('width');
            $table->integer('height');
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
        Schema::dropIfExists('templates');
    }
};
