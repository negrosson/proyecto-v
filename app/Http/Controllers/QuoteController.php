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

        // Consolidar cantidades cuando llega el mismo product_id repetido para impedir evasión del tope de stock
        $aggregatedItems = [];
        foreach ($itemsData as $index => $item) {
            $productId = (int) $item['product_id'];
            $quantity = (int) $item['quantity'];

            if (! isset($aggregatedItems[$productId])) {
                $aggregatedItems[$productId] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'first_index' => $index,
                ];
            } else {
                $aggregatedItems[$productId]['quantity'] += $quantity;
            }
        }

        $productIds = array_keys($aggregatedItems);

        // Consultar únicamente productos activos oficiales para evitar cotizar ítems ocultos o alterar precios
        $products = Product::query()
            ->where('is_active', true)
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $cartItems = [];
        $totalAmount = 0;
        $totalPieces = 0;
        $itemsPayload = [];

        foreach ($aggregatedItems as $productId => $item) {
            /** @var Product|null $product */
            $product = $products->get($productId);
            $firstIndex = $item['first_index'];

            if ($product === null) {
                throw ValidationException::withMessages([
                    "items.{$firstIndex}.product_id" => ['El producto solicitado no se encuentra disponible en el catálogo.'],
                ]);
            }

            if ($item['quantity'] > $product->stock) {
                throw ValidationException::withMessages([
                    "items.{$firstIndex}.quantity" => [
                        "La cantidad solicitada para '{$product->name}' supera el stock disponible ({$product->stock} un.).",
                    ],
                ]);
            }

            $cartItem = new CartItemData(
                code: $product->code,
                name: $product->name,
                unitPrice: $product->rental_price,
                quantity: $item['quantity'],
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
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
            ];
        }

        DB::transaction(function () use ($validated, $totalAmount, $totalPieces, $itemsPayload, $request, $aggregatedItems): void {
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

            foreach ($aggregatedItems as $item) {
                Product::where('id', $item['product_id'])->increment('quoted_count');
                Product::where('id', $item['product_id'])->increment('units_quoted_count', $item['quantity']);
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
