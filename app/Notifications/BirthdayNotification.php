<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BirthdayNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user)
    {
        $this->onQueue('default');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $age = now()->year - $notifiable->fecha_nacimiento->year;

        return (new MailMessage)
            ->greeting("¡Feliz cumpleaños, {$notifiable->name}!")
            ->line("Hoy estás cumpliendo {$age} años.")
            ->line('Que sea un día especial lleno de alegría.')
            ->action('Visita la aplicación', url('/admin'))
            ->line('¡Gracias por ser parte de nuestro equipo!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $age = now()->year - $notifiable->fecha_nacimiento->year;

        return [
            'title' => '¡Feliz cumpleaños!',
            'message' => "Hoy {$notifiable->name} está cumpliendo {$age} años. ¡Que sea un día especial!",
            'user_name' => $notifiable->name,
            'age' => $age,
        ];
    }
}
