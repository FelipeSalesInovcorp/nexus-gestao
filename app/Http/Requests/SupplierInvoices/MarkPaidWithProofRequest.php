<?php

namespace App\Http\Requests\SupplierInvoices;

use Illuminate\Foundation\Http\FormRequest;

class MarkPaidWithProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'send_email' => ['nullable', 'boolean'],
        ];
    }
}
