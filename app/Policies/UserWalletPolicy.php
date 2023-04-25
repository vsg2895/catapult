<?php

namespace App\Policies;

use App\Models\{
    User,
    UserWallet,
};

use Illuminate\Auth\Access\{
    Response,
    HandlesAuthorization,
};

class UserWalletPolicy
{
    use HandlesAuthorization;

    public function update(User $user, UserWallet $userWallet): Response
    {
        return $user->id === $userWallet->user_id ? Response::allow() : Response::deny();
    }

    public function destroy(User $user, UserWallet $userWallet): Response
    {
        return $user->id === $userWallet->user_id ? Response::allow() : Response::deny();
    }
}
