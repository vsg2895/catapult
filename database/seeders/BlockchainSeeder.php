<?php

namespace Database\Seeders;

use App\Models\Blockchain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BlockchainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Blockchain::truncate();
        Schema::enableForeignKeyConstraints();

        $id = 1;

        $coin_types_data = include 'database/seeders/data/blockchains.php';
        $coin_types_insert_data = [];

        foreach ($coin_types_data as $name) {
            $coin_types_insert_data[] = [
                'id' => $id,
                'name' => $name,
            ];

            $id++;
        }

        Blockchain::insert($coin_types_insert_data);
    }
}
