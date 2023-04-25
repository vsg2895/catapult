<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWalletHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_wallet_history', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->bigInteger('value');
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
        Schema::dropIfExists('user_wallet_history');
    }
}
