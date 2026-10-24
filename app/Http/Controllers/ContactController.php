<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Contact\StoreContactMessageAction;
use App\DTOs\ContactMessageData;
use App\Http\Requests\SubmitContactRequest;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /**
     * Procesa y almacena un nuevo mensaje de contacto enviado desde el frontend.
     */
    public function store(SubmitContactRequest $request, StoreContactMessageAction $action): JsonResponse
    {
        $validated = $request->validated();

        $dto = new ContactMessageData(
            name: (string) $validated['name'],
            email: (string) $validated['email'],
            phone: isset($validated['phone']) && is_string($validated['phone']) ? $validated['phone'] : null,
            eventAddress: (string) $validated['event_address'],
            eventDate: isset($validated['event_date']) && is_string($validated['event_date']) ? $validated['event_date'] : null,
            message: (string) $validated['message'],
            ipAddress: $request->ip(),
        );

        $action->execute($dto);

        return response()->json([
            'success' => true,
            'message' => '¡Tu mensaje ha sido recibido con éxito! Nuestro equipo de eventos te contactará a la brevedad.',
        ]);
    }
}
