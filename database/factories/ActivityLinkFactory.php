<?php

namespace Database\Factories;

use App\Models\Link;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'link_id' => Link::factory(),
        ];
    }
}
