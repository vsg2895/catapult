<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskLoserOfContestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private int $taskId;
    private string $taskName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(int $taskId, string $taskName)
    {
        $this->taskId = $taskId;
        $this->taskName = $taskName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line("Competition \"{$this->taskName}\" is over.")
                    ->line("You have not received enough invites to win.");
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
            'type' => 'loser_of_contest',
            'task_id' => $this->taskId,
            'task_name' => $this->taskName,
        ];
    }
}
