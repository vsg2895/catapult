<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationsToSocialProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_providers', function (Blueprint $table) {
            $table->after('name', fn () => $table->json('notifications')->nullable());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_providers', function (Blueprint $table) {
            $table->dropColumn('notifications');
        });
    }
}
