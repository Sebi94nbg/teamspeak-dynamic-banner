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
        Schema::create('banner_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_template_id')->constrained()->cascadeOnDelete();
            $table->integer('x_coordinate');
            $table->integer('y_coordinate');
            $table->string('text');
            $table->integer('font_size')->default(5);
            $table->string('font_color_in_hexadecimal')->default('#000000');
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
        Schema::dropIfExists('banner_configurations');
    }
};
