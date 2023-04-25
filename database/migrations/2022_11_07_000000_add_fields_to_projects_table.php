<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->longText('description')->nullable();
            $table->string('pool_amount')->default('0');
            $table->foreignId('coin_type_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type_of_chain')->nullable();
            $table->string('medium_username')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coin_type_id');
            $table->dropColumn(['description', 'pool_amount', 'type_of_chain', 'medium_username']);
        });
    }
}
