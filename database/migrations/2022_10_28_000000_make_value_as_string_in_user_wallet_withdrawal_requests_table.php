<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeValueAsStringInUserWalletWithdrawalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_wallet_withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn('value');
        });

        Schema::table('user_wallet_withdrawal_requests', function (Blueprint $table) {
            $table->string('value')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_wallet_withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn('value');
        });

        Schema::table('user_wallet_withdrawal_requests', function (Blueprint $table) {
            $table->bigInteger('value');
        });
    }
}
