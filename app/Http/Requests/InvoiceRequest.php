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
            'invoiceDetails.invoiceNumber' => ['required', 'string', 'max:255'],
            'invoiceDetails.invoiceDate' => ['required', 'date'],
            'invoiceDetails.dueDate' => ['nullable', 'date', 'after_or_equal:invoiceDetails.invoiceDate'],
            'invoiceDetails.reference' => ['nullable', 'string', 'max:255'],

            'issuer' => ['required', 'array'],
            'issuer.name' => ['required', 'string', 'max:255'],
            'issuer.street' => ['required', 'string', 'max:255'],
            'issuer.city' => ['required', 'string', 'max:255'],
            'issuer.state' => ['nullable', 'string', 'max:255'],
            'issuer.zip' => ['required', 'string', 'max:20'],
            'issuer.region' => ['nullable', 'string', 'max:255'],
            'issuer.phone' => ['nullable', 'string', 'max:25', 'regex:/^\+?[0-9\s\-()]*$/'],
            'issuer.email' => ['required', 'email', 'max:255'],

            'client' => ['required', 'array'],
            'client.name' => ['required', 'string', 'max:255'],
            'client.street' => ['nullable', 'string', 'max:255'],
            'client.city' => ['nullable', 'string', 'max:255'],
            'client.state' => ['nullable', 'string', 'max:255'],
            'client.zip' => ['nullable', 'string', 'max:20'],
            'client.region' => ['nullable', 'string', 'max:255'],
            'client.phone' => ['nullable', 'string', 'max:25', 'regex:/^\+?[0-9\s\-()]*$/'],
            'client.email' => ['nullable', 'email', 'max:255'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
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
