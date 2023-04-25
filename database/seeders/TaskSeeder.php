<?php

namespace Database\Seeders;

use App\Models\Task;

use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Task::factory()->count(50)->create();
        Task::factory()->count(50)->available()->create();
        Task::factory()->count(50)->finished()->create();
    }
}
