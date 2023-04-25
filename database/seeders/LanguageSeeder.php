<?php

namespace Database\Seeders;

use App\Models\Language;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Language::truncate();
        Schema::enableForeignKeyConstraints();

        $data = include 'database/seeders/data/languages.php';
        $insert_data = [];

        $id = 1;
        foreach ($data as $name) {
            $insert_data[] = [
                'id' => $id,
                'name' => $name,
            ];

            $id++;
        }

        Language::insert($insert_data);
    }
}
