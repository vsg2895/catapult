<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWalletWithdrawalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_wallet_withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('value');
            $table->string('tx_hash');
            $table->string('status')->default('pending');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_wallet_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_wallet_withdrawal_requests');
    }
}
