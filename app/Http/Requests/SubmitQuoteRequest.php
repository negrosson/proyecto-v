<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitQuoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs for validation by stripping control characters and normalizing whitespace.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        $customerName = $this->input('customer_name');
        if (is_string($customerName)) {
            // Eliminar caracteres invisibles de control Unicode (\p{C}) y normalizar espacios
            $cleaned = preg_replace('/[\p{C}]+/u', '', $customerName) ?? $customerName;
            $cleaned = preg_replace('/\s+/u', ' ', $cleaned) ?? $cleaned;
            $sanitized['customer_name'] = trim($cleaned);
        }

        $location = $this->input('location');
        if (is_string($location)) {
            $cleaned = preg_replace('/[\p{C}]+/u', '', $location) ?? $location;
            $cleaned = preg_replace('/\s+/u', ' ', $cleaned) ?? $cleaned;
            $sanitized['location'] = trim($cleaned);
        }

        if ($sanitized !== []) {
            $this->merge($sanitized);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'customer_name' => [
                'required',
                'string',
                'min:3',
                'max:80',
                'regex:/^[\pL\s\.\'-]+$/u',
            ],
            'location' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\pL\pN\s\,\.\#-]+$/u',
            ],
            'event_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
                'before:+2 years',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],
            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:5000',
            ],
        ];
    }

    /**
     * Custom message for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'El nombre del cliente es obligatorio.',
            'customer_name.min' => 'El nombre debe contener al menos 3 caracteres.',
            'customer_name.max' => 'El nombre no puede superar los 80 caracteres.',
            'customer_name.regex' => 'El nombre contiene caracteres inválidos o no permitidos.',
            'location.required' => 'La comuna o lugar del evento es obligatoria.',
            'location.min' => 'El lugar del evento debe contener al menos 3 caracteres.',
            'location.max' => 'El lugar no puede superar los 100 caracteres.',
            'location.regex' => 'El lugar contiene caracteres inválidos o no permitidos.',
            'event_date.date' => 'La fecha del evento debe ser una fecha válida.',
            'event_date.after_or_equal' => 'La fecha del evento no puede ser anterior a hoy.',
            'event_date.before' => 'La fecha del evento no puede superar los 2 años en el futuro.',
            'items.required' => 'Debes incluir al menos un producto en la cotización.',
            'items.min' => 'Debes incluir al menos un producto en la cotización.',
            'items.max' => 'No puedes cotizar más de 50 artículos diferentes a la vez.',
            'items.*.product_id.required' => 'El producto es obligatorio.',
            'items.*.product_id.exists' => 'El producto seleccionado no existe en el catálogo.',
            'items.*.quantity.required' => 'La cantidad es obligatoria.',
            'items.*.quantity.min' => 'La cantidad debe ser de al menos 1 unidad.',
            'items.*.quantity.max' => 'La cantidad máxima por producto es de 5.000 unidades.',
        ];
    }
}
