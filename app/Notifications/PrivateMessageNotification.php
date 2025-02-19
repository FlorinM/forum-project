<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Message;
use App\Models\User;

class PrivateMessageNotification extends Notification
{
    use Queueable;

    /**
     * The message instance associated with the notification.
     *
     * @var \App\Models\Message
     */
    protected $message;

    /**
     * The sender of the message.
     *
     * @var \App\Models\User
     */
    protected $sender;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Message $message The message associated with the notification.
     * @param \App\Models\User $sender The sender of the message.
     */
    public function __construct(Message $message, User $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->message->id,
            'sender' => $this->sender->nickname,
            'discussion_id' => $this->message->discussion_id,
            'created_at' => $this->message->created_at,
        ];
    }
}
