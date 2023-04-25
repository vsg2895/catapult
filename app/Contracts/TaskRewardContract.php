<?php

namespace App\Contracts;

use App\Models\{User, Task};

interface TaskRewardContract
{
    public function giveTo(User $user, Task $task);
}
