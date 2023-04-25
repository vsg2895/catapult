<?php

namespace Database\Seeders;

use App\Models\SocialLink;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SocialLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        SocialLink::truncate();
        Schema::enableForeignKeyConstraints();

        $id = 1;

        $social_links_data = include 'database/seeders/data/social-links.php';
        $social_links_insert_data = [];

        foreach ($social_links_data as $data) {
            foreach ($data['assigned_to'] as $assigned_to) {
                $social_links_insert_data[] = [
                    'id' => $id,
                    'name' => $data['name'],
                    'order' => $data['order'],
                    'assigned_to' => $assigned_to,
                ];

                $id++;
            }
        }

        SocialLink::insert($social_links_insert_data);
    }
}
