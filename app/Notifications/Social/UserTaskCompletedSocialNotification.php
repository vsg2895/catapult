<?php

namespace App\Notifications\Social;

use App\Models\UserTask;
use App\Models\TaskReward;
use App\Notifications\Messages\DiscordMessage;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserTaskCompletedSocialNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $userTask;
    private string $userName;

    private function discordProviderNotification(mixed $notifiable)
    {
        return collect($notifiable->discordProvider()?->notifications)->get('completedTask');
    }

    /**
     * Create a new notification instance.
     *
     * @param UserTask $userTask
     * @param string $userName
     * @return void
     */
    public function __construct(UserTask $userTask, string $userName)
    {
        $this->userTask = $userTask;
        $this->userName = $userName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ($this->discordProviderNotification($notifiable)['active'] ?? null) ? ['discord'] : [];
    }

    /**
     * @param  mixed  $notifiable
     * @return DiscordMessage
     */
    public function toDiscord(mixed $notifiable): DiscordMessage
    {
        $taskUrl = sprintf(
            '%s/take-task/%s',
            config('app.ambassador_frontend_url'),
            $this->userTask->task_id,
        );

        $projectUrl = sprintf(
            '%s/projects/%s',
            config('app.ambassador_frontend_url'),
            $this->userTask->task->project_id,
        );

        $rewards = [];
        foreach ($this->userTask->task->rewards as $reward) {
            $rewards[] = $this->rewardToDiscordFormat($reward);
        }

        return (new DiscordMessage)
            ->embeds([
                [
                    'color' => 3553599,
                    'timestamp' => now()->toISOString(),
                    'title' => $this->userName . ' completed a task ✅',
                    'description' => 'Click on this message to join Catapult and start to contribute',
                    'fields' => [
                        [
                            'name' => 'Task',
                            'value' => '['.$this->userTask->task->name.']('.$taskUrl.')',
                            'inline' => false,
                        ],
                        [
                            'name' => 'Reviewer',
                            'value' => $this->userName,
                            'inline' => false,
                        ],
                        [
                            'name' => 'Rewards',
                            'value' => implode('\\n', $rewards),
                            'inline' => true,
                        ],
                    ],
                    'author' => [
                        'url' => $projectUrl,
                        'name' => 'Catapult',
                        'icon_url' => 'https://catapult.ac/favicons/favicon-32x32.png',
                    ],
                    'url' => $projectUrl,
                ],
            ])
            ->channelId($this->discordProviderNotification($notifiable)['channelId'] ?? null);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            //
        ];
    }

    private function rewardToDiscordFormat(TaskReward $reward)
    {
        if ($reward->type === 'coins') {
            return $reward->formatted_value.' coins';
        }

        if ($reward->type === 'discord_role') {
            return '<@&'.$reward->value.'>';
        }

        return $reward->value;
    }
}
