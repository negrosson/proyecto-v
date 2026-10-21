<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class QuoteRequestData
{
    /**
     * @param  string  $customerName  Nombre del cliente solicitante
     * @param  string|null  $eventDate  Fecha tentativa del evento (formato libre o Y-m-d)
     * @param  string  $location  Comuna, dirección o lugar del evento
     * @param  array<int, CartItemData>  $items  Colección de ítems incluidos en la cotización
     */
    public function __construct(
        public string $customerName,
        public ?string $eventDate,
        public string $location,
        public array $items,
    ) {}

    /**
     * Calcula el monto total estimado de la cotización sumando los subtotales de cada ítem.
     */
    public function total(): int
    {
        return array_reduce(
            $this->items,
            static fn (int $carry, CartItemData $item): int => $carry + $item->subtotal(),
            0
        );
    }
}
