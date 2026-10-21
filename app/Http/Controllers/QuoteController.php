<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Catalog\BuildWhatsAppQuoteUrlAction;
use App\DTOs\CartItemData;
use App\DTOs\QuoteRequestData;
use App\Http\Requests\SubmitQuoteRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class QuoteController extends Controller
{
    public function __construct(
        private readonly BuildWhatsAppQuoteUrlAction $buildWhatsAppQuoteUrl,
    ) {}

    /**
     * Valida, procesa la cotización y retorna la URL generada para WhatsApp.
     *
     * @throws ValidationException
     */
    public function store(SubmitQuoteRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $itemsData = $validated['items'];

        $productIds = array_column($itemsData, 'product_id');

        // Consultar productos oficiales de la base de datos para evitar alteración de precios o códigos
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $cartItems = [];

        foreach ($itemsData as $index => $item) {
            /** @var Product|null $product */
            $product = $products->get($item['product_id']);

            if ($product === null) {
                throw ValidationException::withMessages([
                    "items.{$index}.product_id" => ['El producto solicitado no fue encontrado.'],
                ]);
            }

            if ($item['quantity'] > $product->stock) {
                throw ValidationException::withMessages([
                    "items.{$index}.quantity" => [
                        "La cantidad solicitada para '{$product->name}' supera el stock disponible ({$product->stock} un.).",
                    ],
                ]);
            }

            $cartItems[] = new CartItemData(
                code: $product->code,
                name: $product->name,
                unitPrice: $product->rental_price,
                quantity: (int) $item['quantity'],
            );
        }

        $quoteRequest = new QuoteRequestData(
            customerName: (string) $validated['customer_name'],
            eventDate: isset($validated['event_date']) && is_string($validated['event_date'])
                ? $validated['event_date']
                : null,
            location: (string) $validated['location'],
            items: $cartItems,
        );

        $whatsappUrl = $this->buildWhatsAppQuoteUrl->execute($quoteRequest);

        return response()->json([
            'whatsapp_url' => $whatsappUrl,
        ]);
    }
}
