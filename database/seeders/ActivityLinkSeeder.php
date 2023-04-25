<?php

namespace Database\Seeders;

use App\Models\ActivityLink;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ActivityLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        ActivityLink::truncate();
        Schema::enableForeignKeyConstraints();

        $links_data = include 'database/seeders/data/links.php';
        $activity_links_insert_data = [];

        $link_id = 0;
        $activity_link_id = 0;

        $activity_id = 1;
        foreach ($links_data as $links) {
            foreach ($links as $link) {
                $activity_links_insert_data[] = [
                    'id' => ++$activity_link_id,
                    'link_id' => ++$link_id,
                    'activity_id' => $activity_id,
                ];
            }

            $activity_id++;
        }

        ActivityLink::insert($activity_links_insert_data);
    }
}
