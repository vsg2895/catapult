<?php

namespace App\TaskVerifiers;

use App\Models\SocialProvider;
use App\Contracts\TwitterServiceContract;

class BaseTwitterTaskVerifier extends BaseTaskVerifier
{
    /**
     * @param SocialProvider $socialProvider
     * @param TwitterServiceContract $twitterService
     */
    public function __construct(
        SocialProvider $socialProvider,
        protected TwitterServiceContract $twitterService
    )
    {
        parent::__construct($socialProvider);
    }
}
