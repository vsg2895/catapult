<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeProviderIdNotUniqueInSocialProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_providers', function (Blueprint $table) {
            $table->dropColumn('provider_id');
        });

        Schema::table('social_providers', function (Blueprint $table) {
            $table->string('provider_id')->after('model_id')->nullable();
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
            $table->dropColumn('provider_id');
        });

        Schema::table('social_providers', function (Blueprint $table) {
            $table->string('provider_id')->after('model_id')->unique()->nullable();
        });
    }
}
