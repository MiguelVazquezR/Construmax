<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedActivityNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $activity, public $user_name)
    {
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
            ->subject('Nueva actividad asignada')
            ->markdown('emails.default-template', [
                'greeting' => '¡Hola!',
                'intro' => "$this->user_name te ha asignado la actividad '{$this->activity->name}', ve a revisarla",
                'url' => route('crm.opportunity-tasks.show', $this->activity->id),
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "<span class='text-primary'>$this->user_name</span> te ha asignado la actividad <span class='text-primary'>{$this->activity->name}</span>, ve a revisarla",
            'module' => "projects",
            'url' => route('crm.opportunity-tasks.show', $this->activity->id),
        ];
    }
}
