<?php

namespace Database\Seeders;

use App\Models\CoinType;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CoinTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        CoinType::truncate();
        Schema::enableForeignKeyConstraints();

        $id = 1;

        $coin_types_data = include 'database/seeders/data/coin-types.php';
        $coin_types_insert_data = [];

        foreach ($coin_types_data as $name) {
            $coin_types_insert_data[] = [
                'id' => $id,
                'name' => $name,
            ];

            $id++;
        }

        CoinType::insert($coin_types_insert_data);
    }
}
