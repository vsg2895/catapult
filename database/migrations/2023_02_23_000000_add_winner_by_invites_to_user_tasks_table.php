<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWinnerByInvitesToUserTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->after('referral_code', fn () => $table->boolean('winner_by_invites')->default(false));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->dropColumn('winner_by_invites');
        });
    }
}
