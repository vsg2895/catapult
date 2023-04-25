<?php

namespace App\Contracts;

use App\Models\{User, Task};

interface TaskConditionContract
{
    public function check(User $user, Task $task): bool;
}
