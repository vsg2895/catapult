<?php

namespace Database\Seeders;

use App\Models\Link;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Link::truncate();
        Schema::enableForeignKeyConstraints();

        $links_data = include 'database/seeders/data/links.php';
        $links_insert_data = [];

        $id = 0;
        foreach ($links_data as $links) {
            foreach ($links as $name) {
                $links_insert_data[] = [
                    'id' => ++$id,
                    'name' => $name,
                ];
            }
        }

        Link::insert($links_insert_data);
    }
}
