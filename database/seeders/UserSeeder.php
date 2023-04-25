<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserTask;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->hasTasks(UserTask::factory()->count(50))
            ->hasTasks(UserTask::factory()->count(50), [
                'status' => UserTask::STATUS_DONE,
            ])
            ->hasTasks(UserTask::factory()->count(50), [
                'status' => UserTask::STATUS_RETURNED,
            ])
            ->hasTasks(UserTask::factory()->count(50), [
                'status' => UserTask::STATUS_WAITING_FOR_REVIEW,
            ])
            ->count(50)
            ->create();

        User::factory()
            ->count(20)
            ->create();

        User::factory()
            ->count(20)
            ->create();
    }
}
