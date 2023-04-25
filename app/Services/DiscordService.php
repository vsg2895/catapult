<?php

namespace App\Services;

use App\Contracts\DiscordServiceContract;

use Exception;
use Illuminate\Support\Facades\Http;

class DiscordService implements DiscordServiceContract
{
    public function getGuild(string $id): array
    {
        try {
            $response = Http::baseUrl(config('services.discord_bot.endpoint'))
                ->get(sprintf('guilds/%s', $id));

            $data = $response->json();
            if (empty($data) || !$response->ok()) {
                return [];
            }

            return [
                'id' => $data['id'],
                'name' => $data['name'],
                'roles' => $data['roles'],
                'channels' => $data['channels'],
            ];
        } catch (Exception) {
            return [];
        }
    }

    public function giveRole(string $roleId, string $guildId, string $memberId): bool
    {
        try {
            $response = Http::baseUrl(config('services.discord_bot.endpoint'))
                ->post(sprintf('guilds/%s/add-role', $guildId), [
                    'roleId' => $roleId,
                    'memberId' => $memberId,
                ]);

            $data = $response->json();
            if (empty($data) || !$response->ok()) {
                return false;
            }

            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function checkRole(string $roleId, string $guildId, string $memberId): bool
    {
        try {
            $response = Http::baseUrl(config('services.discord_bot.endpoint'))
                ->get(sprintf(
                    'guilds/%s/members/%s/roles',
                    $guildId,
                    $memberId,
                ));

            $data = $response->json();
            if (empty($data) || !$response->ok()) {
                return false;
            }

            return collect($data)->pluck('id')->contains($roleId);
        } catch (Exception) {
            return false;
        }
    }

    public function getGuildRoles(string $id): array
    {
        try {
            $response = Http::baseUrl(config('services.discord_bot.endpoint'))
                ->get(sprintf('guilds/%s/roles', $id));

            $data = $response->json();
            if (empty($data) || !$response->ok()) {
                return [];
            }

            return $data;
        } catch (Exception) {
            return [];
        }
    }
}
