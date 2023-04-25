<?php

namespace Database\Seeders;

use App\Models\Link;

use Illuminate\Database\Seeder;

class LinkIconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $icons_data = include 'database/seeders/data/link-icons.php';
        $id = 0;

        foreach ($icons_data as $icons) {
            foreach ($icons as $icon) {
                Link::findOrFail(++$id)->addMedia($icon)->preservingOriginal()->toMediaCollection();
            }
        }
    }
}
