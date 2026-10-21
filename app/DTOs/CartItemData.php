<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class CartItemData
{
    /**
     * @param  int  $code  Código numérico único del producto
     * @param  string  $name  Nombre o descripción del ítem
     * @param  int  $unitPrice  Valor de arriendo unitario en CLP
     * @param  int  $quantity  Cantidad de unidades requeridas
     */
    public function __construct(
        public int $code,
        public string $name,
        public int $unitPrice,
        public int $quantity,
    ) {}

    /**
     * Calcula el subtotal del ítem en pesos chilenos (CLP).
     */
    public function subtotal(): int
    {
        return $this->unitPrice * $this->quantity;
    }
}
