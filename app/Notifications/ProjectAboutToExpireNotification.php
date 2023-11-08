<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectAboutToExpireNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $project)
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
            ->subject('Proyecto por expirar')
            ->markdown('emails.default-template', [
                'greeting' => '¡Hola!',
                'intro' => "Hoy es el día límite para terminar el proyecto {$this->project->name}",
                'url' => route('pms.projects.show', $this->project->id),
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Hoy es el día límite para terminar el proyecto <span class='text-primary'>{$this->project->name}</span>",
            'module' => "projects",
            'url' => route('pms.projects.show', $this->project->id),
        ];
    }
}
