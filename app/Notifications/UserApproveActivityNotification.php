<?php

namespace App\Notifications;

use App\Models\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class UserApproveActivityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var string
     */
    private string $activityNames;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $activityNames, User $user)
    {
        $this->user = $user;
        $this->activityNames = $activityNames;
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
            'type' => 'ambassador_activity',
            'activity_name' => $this->activityNames,
            'ambassador_id' => $this->user->id,
            'ambassador_name' => $this->user->name,
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
            'type' => 'ambassador_activity',
            'activity_name' => $this->activityNames,
            'ambassador_id' => $this->user->id,
            'ambassador_name' => $this->user->name,
        ]);
    }

    public function broadcastType(): string
    {
        return 'ambassador_activity';
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
