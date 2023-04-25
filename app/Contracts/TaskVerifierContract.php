<?php

namespace App\Contracts;

use App\Models\Task;

interface TaskVerifierContract
{
    public function verify(Task $task): bool;
}
