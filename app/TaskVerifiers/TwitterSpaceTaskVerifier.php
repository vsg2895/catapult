<?php

namespace App\TaskVerifiers;

use App\Models\Task;
use App\Contracts\TaskVerifierContract;

class TwitterSpaceTaskVerifier extends BaseTwitterTaskVerifier implements TaskVerifierContract
{
    /**
     * @param Task $task
     * @return bool
     */
    public function verify(Task $task): bool
    {
        $space = $this->twitterService->space(getTwitterSpaceId($task->verifier->twitter_space));
        return !empty($space) && $space['state'] !== 'ended';
//            && collect($space['users'])->pluck('id')->contains($this->socialProvider->provider_id);
    }
}
