<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdatedProjectNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $project, public $user_name)
    {
        //
    }

    public function via(object $notifiable): array
    {
        if (app()->environment() === 'local') {
            return ['database'];
        } else {
            return ['database'];
        }
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Proyecto editado')
            ->markdown('emails.default-template', [
                'greeting' => '¡Hola!',
                'intro' => "$this->user_name ha editado el proyecto '{$this->project->name}', ve a revisar los cambios",
                'url' => route('pms.projects.show', $this->project->id),
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "<span class='text-primary'>$this->user_name</span> ha editado el proyecto <span class='text-primary'>{$this->project->name}</span>, ve a revisar los cambios",
            'module' => "projects",
            'url' => route('pms.projects.show', $this->project->id),
        ];
    }
}
