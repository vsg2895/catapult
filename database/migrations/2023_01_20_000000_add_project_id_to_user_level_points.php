<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectIdToUserLevelPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_level_points', function (Blueprint $table) {
            $table->after('activity_id', fn () => $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_level_points', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });
    }
}
