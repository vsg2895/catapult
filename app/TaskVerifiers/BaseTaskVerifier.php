<?php

namespace App\TaskVerifiers;

use App\Models\SocialProvider;

class BaseTaskVerifier
{
    /**
     * @param SocialProvider $socialProvider
     */
    public function __construct(
        protected SocialProvider $socialProvider,
    )
    {
    }
}
