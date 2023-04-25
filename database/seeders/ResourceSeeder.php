<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('resources')->insert([
            'id' => 1,
            'name' => 'Youtube',
            'enabled' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('resources')->insert([
            'id' => 2,
            'name' => 'Instagram',
            'enabled' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('resources')->insert([
            'id' => 3,
            'name' => 'TikTok',
            'enabled' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('resources')->insert([
            'id' => 4,
            'name' => 'Telegram',
            'enabled' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
