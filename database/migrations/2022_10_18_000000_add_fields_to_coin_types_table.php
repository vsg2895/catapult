<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCoinTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coin_types', function (Blueprint $table) {
            $table->json('abi')->nullable();
            $table->string('rpc_url')->nullable();
            $table->integer('chain_id')->default(1);
            $table->string('block_explorer_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coin_types', function (Blueprint $table) {
            $table->dropColumn(['abi', 'rpc_url', 'chain_id', 'block_explorer_url']);
        });
    }
}
