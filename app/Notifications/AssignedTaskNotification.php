<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedTaskNotification extends Notification
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
            ->subject('Nueva tarea asignada')
            ->markdown('emails.assigned-task', [
                'greeting' => '¡Hola!',
                'intro' => "<span class='text-primary'>$notifiable->name</span> te ha asignado la tarea <span class='text-primary'>{$this->task->name}</span>, ve a revisarla",
                'url' => route('pms.projects.show', ['project' => $this->task->project->id, 'defaultTab' => 2]),
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "<span class='text-primary'>$notifiable->name</span> te ha asignado la tarea <span class='text-primary'>{$this->task->name}</span>, ve a revisarla",
            'module' => "projects",
            'url' => route('pms.projects.show', ['project' => $this->task->project->id, 'defaultTab' => 2]),
        ];
    }
}
