<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\BirthdayNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

#[Signature('app:birthday-notification')]
#[Description('Envía notificaciones de cumpleaños a los usuarios que cumplen años hoy')]
class BirthdayNotificationCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Buscar usuarios que cumplen años hoy (comparar mes y día)
        $today = now();
        $usersWithBirthday = User::whereRaw(
            "strftime('%m-%d', fecha_nacimiento) = ?",
            [$today->format('m-d')]
        )->get();

        if ($usersWithBirthday->isEmpty()) {
            $this->info('No hay usuarios que cumplan años hoy.');

            return self::SUCCESS;
        }

        $this->info("Enviando notificaciones de cumpleaños a {$usersWithBirthday->count()} usuario(s)...");

        // Enviar notificación a cada usuario que cumple años
        foreach ($usersWithBirthday as $user) {
            Notification::send($user, new BirthdayNotification($user));
            $this->line("✓ Notificación enviada a {$user->name}");
        }

        $this->info('¡Notificaciones de cumpleaños enviadas exitosamente!');

        return self::SUCCESS;
    }
}
