<?php

namespace Database\Seeders;

use App\Models\Tag;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Tag::truncate();
        Schema::enableForeignKeyConstraints();

        $id = 1;

        $tags_data = include 'database/seeders/data/tags.php';
        $tags_insert_data = [];

        foreach ($tags_data as $name) {
            $tags_insert_data[] = [
                'id' => $id,
                'name' => $name,
            ];

            $id++;
        }

        Tag::insert($tags_insert_data);
    }
}
