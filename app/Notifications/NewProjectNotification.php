<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProjectNotification extends Notification
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
            ->subject('Nuevo proyecto')
            ->markdown('emails.default-template', [
                'greeting' => '¡Hola!',
                'intro' => "Eres participante en un nuevo proyecto llamado '{$this->project->name}', creado por <span class='text-primary'>$this->user_name</span>",
                'url' => route('pms.projects.show', $this->project->id),
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Eres participante en un nuevo proyecto llamado <span class='text-primary'>{$this->project->name}</span> creado por <span class='text-primary'>$this->user_name</span>",
            'module' => "projects",
            'url' => route('pms.projects.show', $this->project->id),
        ];
    }
}
