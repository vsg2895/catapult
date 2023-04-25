<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscordGuildFieldsToTaskVerifiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_verifiers', function (Blueprint $table) {
            $table->after('default_tweet', function () use ($table) {
                $table->string('discord_guild_id')->nullable();
                $table->string('discord_guild_name')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_verifiers', function (Blueprint $table) {
            $table->dropColumn(['discord_guild_id', 'discord_guild_name']);
        });
    }
}
