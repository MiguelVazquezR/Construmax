<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $concept_type, public $concept_name, public $module, public $url)
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
            ->subject('Nuevo comentario')
            ->markdown('emails.new-comment', [
                'greeting' => '¡Hola!',
                'intro' => "$notifiable->name hizo un comentario en la $this->concept_type <span class='text-primary'>{$this->concept_name}</span>",
                'url' => $this->url,
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "$notifiable->name hizo un comentario en la $this->concept_type <span class='text-primary'>{$this->concept_name}</span>",
            'module' => $this->module,
            'url' => $this->url,
        ];
    }
}
