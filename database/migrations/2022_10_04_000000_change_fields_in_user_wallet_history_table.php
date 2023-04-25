<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldsInUserWalletHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_wallet_history', function (Blueprint $table) {
            $table->dropColumn('action');
            $table->integer('points');
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_wallet_history', function (Blueprint $table) {
            $table->string('action');
            $table->dropColumn('points');
            $table->dropConstrainedForeignId('task_id');
        });
    }
}
