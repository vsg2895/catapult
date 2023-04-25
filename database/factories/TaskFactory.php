<?php

namespace Database\Factories;

use App\Models\Project;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(2),
            'text' => $this->faker->paragraph(5),
            'coins' => $this->faker->numberBetween(100, 1000),
            'project_id' => Project::inRandomOrder()->first(),
            'resource_id' => Resource::inRandomOrder()->first(),
            'started_at' => $this->faker->dateTimeBetween('now', '+10 days'),
            'ended_at' => $this->faker->dateTimeBetween('now', '+20 days'),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'started_at' => $this->faker->dateTimeBetween('now'),
            ];
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function finished()
    {
        return $this->state(function (array $attributes) {
            return [
                'started_at' => $this->faker->dateTimeBetween('-01 minutes'),
                'ended_at' => $this->faker->dateTimeBetween('now'),
            ];
        });
    }
}
