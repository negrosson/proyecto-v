<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Catalog\BuildWhatsAppQuoteUrlAction;
use App\DTOs\CartItemData;
use App\DTOs\QuoteRequestData;
use App\Http\Requests\SubmitQuoteRequest;
use App\Models\Product;
use App\Models\QuoteLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
        $totalAmount = 0;
        $totalPieces = 0;
        $itemsPayload = [];

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

            $cartItem = new CartItemData(
                code: $product->code,
                name: $product->name,
                unitPrice: $product->rental_price,
                quantity: (int) $item['quantity'],
            );

            $cartItems[] = $cartItem;
            $subtotal = $cartItem->subtotal();
            $totalAmount += $subtotal;
            $totalPieces += $cartItem->quantity;

            $itemsPayload[] = [
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'unit_price' => $product->rental_price,
                'quantity' => (int) $item['quantity'],
                'subtotal' => $subtotal,
            ];
        }

        DB::transaction(function () use ($validated, $totalAmount, $totalPieces, $itemsPayload, $request, $itemsData): void {
            QuoteLog::create([
                'customer_name' => (string) $validated['customer_name'],
                'event_date' => isset($validated['event_date']) && is_string($validated['event_date'])
                    ? $validated['event_date']
                    : null,
                'location' => (string) $validated['location'],
                'total_amount' => $totalAmount,
                'items_count' => $totalPieces,
                'items_payload' => $itemsPayload,
                'status' => 'pendiente',
                'ip_address' => $request->ip(),
            ]);

            foreach ($itemsData as $item) {
                Product::where('id', $item['product_id'])->increment('quoted_count');
                Product::where('id', $item['product_id'])->increment('units_quoted_count', (int) $item['quantity']);
            }
        });

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
