<?php

namespace Database\Factories;

use App\Models\{
    Activity,
    ActivityLink,
};

use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Activity $activity) {
            ActivityLink::factory(['activity_id' => $activity->getKey()])->count(5)->create();
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(2),
        ];
    }
}
