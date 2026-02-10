<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajuste aqui quando existir permissões específicas (ex: config.company.edit)
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:16'],
            'locality' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:32'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }

    public function validatedPayload(): array
    {
        $validated = $this->validated();

        return [
            'name' => $validated['name'] ?? null,
            'address' => $validated['address'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'locality' => $validated['locality'] ?? null,
            'tax_number' => $validated['tax_number'] ?? null,
            'remove_logo' => (bool)($validated['remove_logo'] ?? false),
        ];
    }
}
