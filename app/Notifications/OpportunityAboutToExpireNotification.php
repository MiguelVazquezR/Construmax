<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OpportunityAboutToExpireNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $opportunity)
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
            ->subject('Oportunidad por expirar')
            ->markdown('emails.default-template', [
                'greeting' => '¡Hola!',
                'intro' => "Hoy es el día límite para terminar la oportunidad {$this->opportunity->name}",
                'url' => route('crm.opportunities.show', $this->opportunity->id),
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Hoy es el día límite para terminar la oportunidad <span class='text-primary'>{$this->opportunity->name}</span>",
            'module' => "opportunitys",
            'url' => route('crm.opportunities.show', $this->opportunity->id),
        ];
    }
}
