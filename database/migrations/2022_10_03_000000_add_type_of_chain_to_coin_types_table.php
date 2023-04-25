<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeOfChainToCoinTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coin_types', function (Blueprint $table) {
            $table->string('type_of_chain')->default('ERC-20');
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
            $table->dropColumn('type_of_chain');
        });
    }
}
