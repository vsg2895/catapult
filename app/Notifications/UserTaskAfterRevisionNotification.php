<?php

namespace App\Notifications;

use App\Models\UserTask;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class UserTaskAfterRevisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var UserTask
     */
    private UserTask $userTask;

    /**
     * @var string
     */
    private string $ambassadorName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $ambassadorName, UserTask $userTask)
    {
        $this->userTask = $userTask;
        $this->ambassadorName = $ambassadorName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['database', 'broadcast'];
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
            'type' => 'task_after_revision',
            'task_id' => $this->userTask->id,
            'task_name' => $this->userTask->task->name,
            'ambassador_name' => $this->ambassadorName,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast(mixed $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'task_after_revision',
            'task_id' => $this->userTask->id,
            'task_name' => $this->userTask->task->name,
            'ambassador_name' => $this->ambassadorName,
        ]);
    }

    public function broadcastType(): string
    {
        return 'task_after_revision';
    }

    /**
     * Determine which connections should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaConnections(): array
    {
        return [
            'database' => 'sync',
            'broadcast' => 'sync',
        ];
    }
}
