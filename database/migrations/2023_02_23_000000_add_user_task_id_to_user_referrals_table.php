<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserTaskIdToUserReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_referrals', function (Blueprint $table) {
            $table->after('task_id', fn () => $table->foreignId('user_task_id')->nullable()->constrained()->cascadeOnDelete());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_referrals', function (Blueprint $table) {
            $table->dropColumn('user_task_id');
        });
    }
}
