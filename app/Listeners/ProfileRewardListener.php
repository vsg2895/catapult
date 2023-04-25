<?php

namespace App\Listeners;

use App\Events\UserUpdated;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProfileRewardListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserUpdated  $event
     * @return void
     */
    public function handle(UserUpdated $event): void
    {
        $user = $event->user;
        $user->load([
            'skills',
            'skills.skill',
            'country',
            'country.country',
            'languages',
            'languages.language',
            'activities',
            'activities.activity',
            'socialProviders',
        ]);

        if ($user->set_up_profile['percentage'] === 100 && !$user->completed_profile_reward) {
            $user->update([
                'points' => DB::raw('points + 20'),
                'completed_profile_reward' => 1,
            ]);
        }
    }
}
