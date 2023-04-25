<?php

namespace App\TaskVerifiers;

use App\Models\Task;
use App\Contracts\TaskVerifierContract;

class TwitterFollowTaskVerifier extends BaseTwitterTaskVerifier implements TaskVerifierContract
{
    /**
     * @param Task $task
     * @return bool
     */
    public function verify(Task $task): bool
    {
        $user = $this->twitterService->user(getTwitterUsername($task->verifier->twitter_follow));
        if (empty($user)) {
            return false;
        }

        return collect($this->twitterService->userFollowers($user['id']))->pluck('id')
            ->contains($this->socialProvider->provider_id);
    }
}
