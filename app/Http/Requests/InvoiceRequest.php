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
            'issuer.street' => ['nullable', 'string'],
            'issuer.city' => ['nullable', 'string'],
            'issuer.state' => ['nullable', 'string'],
            'issuer.zip' => ['nullable', 'string'],
            'issuer.region' => ['nullable', 'string'],
            'issuer.phone' => ['nullable', 'string'],
            'issuer.email' => ['nullable', 'email'],

            'client' => ['required', 'array'],
            'client.name' => ['required', 'string'],
            'client.street' => ['nullable', 'string'],
            'client.city' => ['nullable', 'string'],
            'client.state' => ['nullable', 'string'],
            'client.zip' => ['nullable', 'string'],
            'client.region' => ['nullable', 'string'],
            'client.phone' => ['nullable', 'string'],
            'client.email' => ['nullable', 'email'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],

            'totals' => ['required', 'array'],
            'totals.currency' => ['required', 'string', 'in:EUR,USD,GBP,CHF'],
            'totals.taxRate' => ['required', 'numeric', 'min:0', 'max:100'],
            'totals.discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'totals.shipping' => ['nullable', 'numeric', 'min:0'],
            'totals.deposit' => ['nullable', 'numeric', 'min:0'],
            'totals.payments' => ['nullable', 'numeric', 'min:0'],
            'totals.sum' => ['required', 'numeric', 'min:0'],
            'totals.totalNet' => ['required', 'numeric', 'min:0'],
            'totals.totalGross' => ['required', 'numeric', 'min:0'],
            'totals.amountDue' => ['required', 'numeric', 'min:0'],
        ];
    }
}
