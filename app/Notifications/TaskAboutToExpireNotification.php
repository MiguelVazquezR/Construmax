<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAboutToExpireNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $task)
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
            ->subject('Tarea por expirar')
            ->markdown('emails.default-template', [
                'greeting' => '¡Hola!',
                'intro' => "Hoy es el día límite para terminar la tarea '{$this->task->name}'",
                'url' => route('pms.projects.show', ['project' => $this->task->project->id, 'defaultTab' => 2]),
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Hoy es el día límite para terminar la tarea <span class='text-primary'>{$this->task->name}</span>",
            'module' => "projects",
            'url' => route('pms.projects.show', ['project' => $this->task->project->id, 'defaultTab' => 2]),
        ];
    }
}
