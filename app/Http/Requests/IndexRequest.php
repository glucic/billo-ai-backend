<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'in:created_at,invoice_number,client_name,total'],
            'sort_order' => ['sometimes', 'in:asc,desc'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }

}