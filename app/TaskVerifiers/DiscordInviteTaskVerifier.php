<?php

namespace App\TaskVerifiers;

use App\Models\Task;
use App\Contracts\TaskVerifierContract;

use Illuminate\Support\Facades\Http;

class DiscordInviteTaskVerifier extends BaseTaskVerifier implements TaskVerifierContract
{
    /**
     * @param Task $task
     * @return bool
     */
    public function verify(Task $task): bool
    {
        $response = Http::baseUrl(config('services.discord_bot.endpoint'))
            ->get(sprintf(
                'guilds/%s/members/%s',
                $task->verifier->discord_guild_id,
                $this->socialProvider->provider_id,
            ));

        return $response->ok();
    }
}
