<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SocialLinkSeeder::class);
        $this->call(CoinTypeSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(SkillSeeder::class);
        $this->call(LinkSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(LinkIconSeeder::class);
        $this->call(ActivityLinkSeeder::class);
        $this->call(SocialLinkIconSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
