<?php

namespace App\TaskVerifiers;

use App\Models\Task;
use App\Contracts\TaskVerifierContract;

class TwitterPostTaskVerifier extends BaseTwitterTaskVerifier implements TaskVerifierContract
{
    /**
     * @param Task $task
     * @return bool
     */
    public function verify(Task $task): bool
    {
        $userTweet = getTwitterTweetId(request()->input('user_tweet'));
        if (empty($userTweet)) {
            return false;
        }

        $tweet = $this->twitterService->tweet($userTweet);
        if (empty($tweet) || $tweet['author_id'] !== $this->socialProvider->provider_id) {
            return false;
        }

        $words = $task->verifier->tweet_words;
        if (!empty($words)) {
            $pattern = '/(' . implode('|', $words) . ')/i';
            preg_match_all($pattern, $tweet['text'], $matches);
            return count($words) === collect($matches)->values()->unique()->count();
        }

        return true;
    }
}
