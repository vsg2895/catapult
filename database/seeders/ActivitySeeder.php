<?php

namespace Database\Seeders;

use App\Models\Activity;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Activity::truncate();
        Schema::enableForeignKeyConstraints();

        $id = 1;

        $activites_data = include 'database/seeders/data/activities.php';
        $activities_insert_data = [];

        foreach ($activites_data as $name) {
            $activities_insert_data[] = [
                'id' => $id,
                'name' => $name,
            ];

            $id++;
        }

        Activity::insert($activities_insert_data);
    }
}
