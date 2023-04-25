<?php

namespace App\TaskVerifiers;

use App\Models\Task;
use App\Contracts\TaskVerifierContract;

class TwitterPostRetweetTaskVerifier extends BaseTwitterTaskVerifier implements TaskVerifierContract
{
    /**
     * @param Task $task
     * @return bool
     */
    public function verify(Task $task): bool
    {
        $tweetId = getTwitterTweetId($task->verifier->twitter_tweet);
        return collect($this->twitterService->tweetRetweets($tweetId))->pluck('id')
            ->contains($this->socialProvider->provider_id);
    }
}
