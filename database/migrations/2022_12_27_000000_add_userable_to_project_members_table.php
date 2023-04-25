<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserableToProjectMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_members', function (Blueprint $table) {
            $table->after('manager_id', fn () => $table->morphs('userable'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_members', function (Blueprint $table) {
            $table->dropMorphs('userable');
        });
    }
}
