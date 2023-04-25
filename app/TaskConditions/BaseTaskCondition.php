<?php

namespace App\TaskConditions;

use App\Models\TaskCondition;

class BaseTaskCondition
{
    /**
     * @param TaskCondition $taskCondition
     */
    public function __construct(
        protected TaskCondition $taskCondition,
    )
    {
    }
}
