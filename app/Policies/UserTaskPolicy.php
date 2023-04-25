<?php

namespace App\Policies;

use App\Models\{
    User,
    UserTask,
};

use Illuminate\Auth\Access\{
    Response,
    HandlesAuthorization,
};

class UserTaskPolicy
{
    use HandlesAuthorization;

    public function show(User $user, UserTask $userTask): Response
    {
        return $user->id === $userTask->user_id ? Response::allow() : Response::deny();
    }

    public function claim(User $user, UserTask $userTask): Response
    {
        return $user->id === $userTask->user_id ? Response::allow() : Response::deny();
    }

    public function report(User $user, UserTask $userTask): Response
    {
        return $user->id === $userTask->user_id ? Response::allow() : Response::deny();
    }
}
