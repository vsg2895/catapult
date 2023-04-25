<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RemindTaskExpirationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    private $taskName;

    /**
     * Create a new notification instance.
     *
     * @param string $taskName
     * @return void
     */
    public function __construct(string $taskName)
    {
        $this->taskName = $taskName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line("Time to complete the task \"$this->taskName\" ends in 2 days, please pay attention to this.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
