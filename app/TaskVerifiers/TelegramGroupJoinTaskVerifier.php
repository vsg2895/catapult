<?php

namespace App\TaskVerifiers;

use App\Models\Task;
use App\Contracts\TaskVerifierContract;

use Exception;
use Illuminate\Support\Facades\Http;

class TelegramGroupJoinTaskVerifier extends BaseTaskVerifier implements TaskVerifierContract
{
    /**
     * @param Task $task
     * @return bool
     */
    public function verify(Task $task): bool
    {
        try {
            $url = config('services.telegram.endpoint') . config('services.telegram.client_secret');
            $chatId = getTelegramChatId($task->verifier->invite_link) ?? $task->verifier->invite_link;

            $response = Http::baseUrl($url)
                ->get('getChatMember', [
                    'chat_id' => is_int($chatId) ? $chatId : (str_starts_with($chatId, '@') ? $chatId : '@'.$chatId),
                    'user_id' => $this->socialProvider->provider_id,
                ]);

            $data = $response->json();
            if (empty($data) || !$response->ok() || !isset($response['ok'])) {
                return false;
            }

            return (bool) $response['ok'];
        } catch (Exception) {
            return false;
        }
    }
}
