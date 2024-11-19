<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('class');
            $table->smallInteger('xp');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users");

        });
    }

    public function down()
    {
        Schema::dropIfExists('players');
    }
};
