<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultLevelInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `users` CHANGE COLUMN `level` `level` INTEGER NOT NULL DEFAULT 0;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `users` CHANGE COLUMN `level` `level` INTEGER NOT NULL DEFAULT 1;');
    }
}
