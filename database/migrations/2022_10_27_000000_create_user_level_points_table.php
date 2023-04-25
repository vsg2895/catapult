<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLevelPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_level_points', function (Blueprint $table) {
            $table->id();
            $table->integer('level');
            $table->integer('points');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('user_task_points');
    }
}
