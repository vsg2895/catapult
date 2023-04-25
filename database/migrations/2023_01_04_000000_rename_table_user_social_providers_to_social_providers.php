<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTableUserSocialProvidersToSocialProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_social_providers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::rename('user_social_providers', 'social_providers');

        Schema::table('social_providers', function (Blueprint $table) {
            $table->after('id', fn () => $table->morphs('model'));
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
            $table->dropMorphs('userable');
        });

        Schema::rename('social_providers', 'user_social_providers');

        Schema::table('user_social_providers', function (Blueprint $table) {
           $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });
    }
}
