<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexFieldsForSearchTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index('name');
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
            $table->dropIndex(['name']);
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
}
