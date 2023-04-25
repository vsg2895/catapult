<?php

namespace Database\Seeders;

use App\Models\Skill;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Skill::truncate();
        Schema::enableForeignKeyConstraints();

        $skills_data = include 'database/seeders/data/skills.php';
        $skills_insert_data = [];

        $activity_id = 1;
        $activity_skill_id = 0;

        foreach ($skills_data as $skills) {
            foreach ($skills as $skill) {
                $skills_insert_data[] = [
                    'id' => ++$activity_skill_id,
                    'name' => $skill,
                    'activity_id' => $activity_id,
                ];
            }

            $activity_id++;
        }

        Skill::insert($skills_insert_data);
    }
}
