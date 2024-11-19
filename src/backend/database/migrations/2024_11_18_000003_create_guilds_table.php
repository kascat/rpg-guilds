<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guilds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('session_id');
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("session_id")->references("id")->on("sessions");

        });
    }

    public function down()
    {
        Schema::dropIfExists('guilds');
    }
};
