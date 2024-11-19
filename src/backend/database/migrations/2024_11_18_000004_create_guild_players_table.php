<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guild_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guild_id');
            $table->unsignedBigInteger('player_id');

            $table->foreign("player_id")->references("id")->on("players");
            $table->foreign("guild_id")->references("id")->on("guilds");

        });
    }

    public function down()
    {
        Schema::dropIfExists('guild_players');
    }
};
