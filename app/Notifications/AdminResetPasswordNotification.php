<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notificación oficial en español para restablecer la contraseña del panel administrativo.
 */
class AdminResetPasswordNotification extends Notification
{
    use Queueable;

    public string $url = '';

    public function __construct(public readonly string $token) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expireMinutes = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);
        $appName = (string) config('app.name', 'VYS · Productora de Eventos');

        return (new MailMessage)
            ->subject("Restablecer contraseña — {$appName}")
            ->greeting('Hola,')
            ->line("Recibimos una solicitud para restablecer la contraseña de acceso al panel administrativo de {$appName}.")
            ->action('Restablecer Contraseña', $this->url)
            ->line("Este enlace de seguridad expirará automáticamente en {$expireMinutes} minutos.")
            ->line('Si no solicitaste este cambio, puedes ignorar este correo con tranquilidad: tu contraseña actual no será modificada.')
            ->salutation("Saludos,\nEquipo de {$appName}");
    }
}
