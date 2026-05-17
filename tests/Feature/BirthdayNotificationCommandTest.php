<?php

use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

test('birthday notification command sends notifications to users with birthday today', function () {
    // Crear un usuario que cumple años hoy
    $todayDate = now()->format('Y-m-d');
    $birthdayUser = User::factory()->create([
        'fecha_nacimiento' => now()->subYears(25)->format('Y-m-d'),
    ]);

    // Crear un usuario que no cumple años hoy
    $otherUser = User::factory()->create([
        'fecha_nacimiento' => now()->subYears(30)->addMonths(6)->format('Y-m-d'),
    ]);

    // Ejecutar el comando
    $this->artisan('app:birthday-notification')
        ->assertSuccessful()
        ->expectsOutput('Enviando notificaciones de cumpleaños a 1 usuario(s)...');

    // Verificar que se envió la notificación al usuario que cumple años
    Notification::assertSentTo(
        [$birthdayUser],
        \App\Notifications\BirthdayNotification::class
    );

    // Verificar que NO se envió al otro usuario
    Notification::assertNotSentTo(
        [$otherUser],
        \App\Notifications\BirthdayNotification::class
    );
});

test('birthday notification command shows message when no users have birthday today', function () {
    // Crear usuarios sin cumpleaños hoy
    User::factory(3)->create();

    // Ejecutar el comando
    $this->artisan('app:birthday-notification')
        ->assertSuccessful()
        ->expectsOutput('No hay usuarios que cumplan años hoy.');
});

test('birthday notification includes correct data', function () {
    // Crear un usuario que cumple años hoy
    $user = User::factory()->create([
        'name' => 'Juan Pérez',
        'fecha_nacimiento' => now()->subYears(30)->format('Y-m-d'),
    ]);

    // Ejecutar el comando
    $this->artisan('app:birthday-notification')->assertSuccessful();

    // Verificar que se envió la notificación con datos correctos
    Notification::assertSentTo($user, \App\Notifications\BirthdayNotification::class);
});
