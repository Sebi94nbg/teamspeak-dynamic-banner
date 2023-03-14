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
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->string('virtualserver_name')->nullable();
            $table->string('host');
            $table->integer('voice_port')->unsigned();
            $table->integer('serverquery_port')->unsigned();
            $table->boolean('is_ssh');
            $table->string('serverquery_username');
            $table->string('serverquery_password');
            $table->string('client_nickname');
            $table->integer('default_channel_id')->nullable();
            $table->boolean('autostart_enabled')->default(true);
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
        Schema::dropIfExists('instances');
    }
};
