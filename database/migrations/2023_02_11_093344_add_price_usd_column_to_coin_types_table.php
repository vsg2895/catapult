<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceUsdColumnToCoinTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coin_types', function (Blueprint $table) {
            $table->unsignedDecimal('price_usd', 15, 10)
                ->after('name')
                ->nullable();
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
            $table->dropColumn('price_usd');
        });
    }
}
