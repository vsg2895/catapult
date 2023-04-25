<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferralCodeToProjectMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_members', function (Blueprint $table) {
            $table->after('project_id', fn () => $table->uuid('referral_code')->nullable());
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
            $table->dropColumn('referral_code');
        });
    }
}
