<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoiceDetails' => ['required', 'array'],
            'invoiceDetails.invoiceNumber' => ['required', 'string'],
            'invoiceDetails.invoiceDate' => ['required', 'date'],
            'invoiceDetails.dueDate' => ['nullable', 'date'],
            'invoiceDetails.reference' => ['nullable', 'string'],

            'issuer' => ['required', 'array'],
            'issuer.name' => ['required', 'string'],
            'issuer.address' => ['nullable', 'string'],
            'issuer.city' => ['nullable', 'string'],
            'issuer.state' => ['nullable', 'string'],
            'issuer.zip' => ['nullable', 'string'],
            'issuer.phone' => ['nullable', 'string'],
            'issuer.email' => ['nullable', 'email'],

            'client' => ['required', 'array'],
            'client.name' => ['required', 'string'],
            'client.address' => ['nullable', 'string'],
            'client.city' => ['nullable', 'string'],
            'client.state' => ['nullable', 'string'],
            'client.zip' => ['nullable', 'string'],
            'client.phone' => ['nullable', 'string'],
            'client.email' => ['nullable', 'email'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}