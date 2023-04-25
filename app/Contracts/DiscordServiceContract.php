<?php

namespace App\Contracts;

interface DiscordServiceContract
{
    public function getGuild(string $id): array;
    public function giveRole(string $roleId, string $guildId, string $memberId): bool;
    public function checkRole(string $roleId, string $guildId, string $memberId): bool;
    public function getGuildRoles(string $id): array;
}
