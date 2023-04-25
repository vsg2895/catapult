<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\UserTask;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => UserTask::STATUS_IN_PROGRESS,
            'report' => null,
            'task_id' => Task::factory()->available(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function done()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => UserTask::STATUS_DONE,
                'rating' => $this->faker->numberBetween(1, 5),
            ];
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function returned()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => UserTask::STATUS_RETURNED,
            ];
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function waitingForReview()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => UserTask::STATUS_WAITING_FOR_REVIEW,
                'report' => $this->faker->paragraph(2),
            ];
        });
    }
}
