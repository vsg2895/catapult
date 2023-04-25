<?php

namespace Database\Seeders;

use App\Models\Country;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Country::truncate();
        Schema::enableForeignKeyConstraints();

        $data = include 'database/seeders/data/countries.php';
        $insert_data = [];

        $id = 1;
        foreach ($data as $code => $name) {
            $insert_data[] = [
                'id' => $id,
                'name' => $name,
            ];

            $id++;
        }

        Country::insert($insert_data);
    }
}
