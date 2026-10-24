<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\ContactMessageData;
use App\Models\ContactMessage;

final class StoreContactMessageAction
{
    /**
     * Almacena una nueva consulta recibida desde el formulario público de contacto.
     */
    public function execute(ContactMessageData $data): ContactMessage
    {
        return ContactMessage::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'event_address' => $data->eventAddress,
            'event_date' => $data->eventDate,
            'message' => $data->message,
            'ip_address' => $data->ipAddress,
            'is_read' => false,
        ]);
    }
}
