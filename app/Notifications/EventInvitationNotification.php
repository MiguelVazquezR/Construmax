<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $calendar)
    {
        //
    }

    public function via(object $notifiable): array
    {
        if (app()->environment() === 'local') {
            return ['database'];
        } else {
            return ['mail', 'database'];
        }
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitación a evento')
            ->markdown('emails.event-invitation', [
                'greeting' => '¡Hola!',
                'intro' => "$notifiable->name te ha invitado al evento <span class='text-primary'>{$this->calendar->title}</span>",
                'url' => route('calendars.index'),
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "$notifiable->name te ha invitado al evento <span class='text-primary'>{$this->calendar->title}</span>",
            'module' => 'calendars',
            'url' => route('calendars.index'),
        ];
    }
}
