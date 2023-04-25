<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLevelFieldsInTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('min_level');
            $table->dropColumn('max_level');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->tinyInteger('min_level')->nullable();
            $table->tinyInteger('max_level')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('min_level');
            $table->dropColumn('max_level');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->tinyInteger('min_level')->default(1);
            $table->tinyInteger('max_level')->default(1);
        });
    }
}
