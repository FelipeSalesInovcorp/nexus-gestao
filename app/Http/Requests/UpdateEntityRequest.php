<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $entityId = $this->route('entity');

        return [
            'is_client' => ['boolean'],
            'is_supplier' => ['boolean'],

            'name' => ['required', 'string', 'max:255'],

            'nif' => [
                'required',
                'string',
                'max:20',
                Rule::unique('entities', 'nif')->ignore($entityId),
            ],

            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'regex:/^\d{4}-\d{3}$/'],
            'city' => ['nullable', 'string', 'max:255'],
            'country_id' => ['nullable', 'exists:countries,id'],

            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],

            'notes' => ['nullable', 'string'],
            'rgpd_consent' => ['boolean'],
            'active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $isClient = (bool) $this->input('is_client', false);
        $isSupplier = (bool) $this->input('is_supplier', false);

        if (!$isClient && !$isSupplier) {
            $this->merge(['is_client' => true]);
        }
    }
}
