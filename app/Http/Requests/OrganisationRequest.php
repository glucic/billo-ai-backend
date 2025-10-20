<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganisationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'street' => ['required', 'nullable', 'string', 'max:255'],
            'city' => ['required', 'nullable', 'string', 'max:255'],
            'zip' => ['required', 'nullable', 'string', 'max:20'],
            'region' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25', 'regex:/^\+?[0-9\s\-()]*$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'employee_count' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
