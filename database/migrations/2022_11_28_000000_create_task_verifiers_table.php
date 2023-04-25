<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskVerifiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_verifiers', function (Blueprint $table) {
            $table->id();
            $table->json('types');
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->string('invite_link')->nullable();
            $table->json('tweet_words')->nullable();
            $table->string('twitter_tweet')->nullable();
            $table->string('twitter_space')->nullable();
            $table->string('twitter_follow')->nullable();
            $table->string('default_reply')->nullable();
            $table->string('default_tweet')->nullable();
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
        Schema::dropIfExists('task_verifiers');
    }
}
