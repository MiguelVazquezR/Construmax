<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionInCommentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $concept_type, public $concept_name, public $module, public $url, public $user_name)
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
            ->subject('Mención en comentario')
            ->markdown('emails.default-template', [
                'greeting' => '¡Hola!',
                'intro' => "$this->user_name te mencionó en un comentario de la $this->concept_type {$this->concept_name}",
                'url' => $this->url,
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "<span class='text-primary'>$this->user_name</span> te mencionó en un comentario de la $this->concept_type <span class='text-primary'>{$this->concept_name}</span>",
            'module' => $this->module,
            'url' => $this->url,
        ];
    }
}
