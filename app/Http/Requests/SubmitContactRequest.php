<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitiza los campos eliminando etiquetas HTML y caracteres invisibles de control.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach (['name', 'email', 'phone', 'event_address', 'message'] as $field) {
            $value = $this->input($field);
            if (is_string($value)) {
                $cleaned = strip_tags($value);
                $cleaned = preg_replace('/[\p{C}]+/u', '', $cleaned) ?? $cleaned;
                $cleaned = preg_replace('/\s+/u', ' ', $cleaned) ?? $cleaned;
                $sanitized[$field] = trim($cleaned);
            }
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
            // Protección Honeypot: debe permanecer vacío
            'website_hp' => [
                'nullable',
                'max:0',
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:80',
                'regex:/^[\pL\s\.\'-]+$/u',
            ],
            'email' => [
                'required',
                'email:rfc',
                'max:120',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'event_address' => [
                'required',
                'string',
                'min:5',
                'max:150',
                'regex:/^[\pL\pN\s\,\.\#-]+$/u',
            ],
            'event_date' => [
                'required',
                'date',
                'after_or_equal:today',
                'before:+2 years',
            ],
            'message' => [
                'required',
                'string',
                'min:10',
                'max:1000',
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
            'website_hp.max' => 'Petición rechazada por detección de bot.',
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe contener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 80 caracteres.',
            'name.regex' => 'El nombre contiene caracteres inválidos.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Por favor ingresa un correo electrónico válido.',
            'event_address.required' => 'La dirección o comuna del evento es obligatoria.',
            'event_address.min' => 'La dirección debe contener al menos 5 caracteres.',
            'event_address.regex' => 'La dirección contiene caracteres inválidos.',
            'event_date.required' => 'La fecha del evento es obligatoria.',
            'event_date.date' => 'La fecha del evento debe ser válida.',
            'event_date.after_or_equal' => 'La fecha del evento no puede ser anterior a hoy.',
            'event_date.before' => 'La fecha del evento no puede superar los 2 años en el futuro.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.min' => 'El mensaje debe contener al menos 10 caracteres.',
            'message.max' => 'El mensaje no puede superar los 1.000 caracteres.',
        ];
    }
}
