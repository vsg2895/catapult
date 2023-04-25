<?php

namespace Database\Seeders;

use App\Models\SocialLink;

use Illuminate\Database\Seeder;

class SocialLinkIconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $icons_data = include 'database/seeders/data/social-link-icons.php';
        foreach ($icons_data as $name => $icon) {
            SocialLink::where(['name' => $name])->get()->each(function ($socialLink) use ($icon) {
                $socialLink->addMedia($icon)->preservingOriginal()->toMediaCollection();
            });
        }
    }
}
