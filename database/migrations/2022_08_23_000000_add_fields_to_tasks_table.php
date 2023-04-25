<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('activity_id')->nullable()->constrained()->cascadeOnDelete();
            $table->tinyInteger('min_level')->default(1);
            $table->tinyInteger('max_level')->default(1);
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
            $table->dropConstrainedForeignId('activity_id');
            $table->dropColumn(['min_level', 'max_level']);
        });
    }
}
