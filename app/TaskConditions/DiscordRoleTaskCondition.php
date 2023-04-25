<?php

namespace App\TaskConditions;

use App\Models\{User, Task};
use App\Contracts\{TaskConditionContract, DiscordServiceContract};

class DiscordRoleTaskCondition extends BaseTaskCondition implements TaskConditionContract
{
    public function check(User $user, Task $task): bool
    {
        $userDiscordProvider = $user->discordProvider();
        if (!$userDiscordProvider) {
            return false;
        }

        $projectDiscordProvider = $task->project->discordProvider();
        if (!$projectDiscordProvider) {
            return false;
        }

        return app(DiscordServiceContract::class)->checkRole(
            $this->taskCondition->value,
            $projectDiscordProvider->provider_id,
            $userDiscordProvider->provider_id,
        );
    }
}
