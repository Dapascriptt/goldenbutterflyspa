<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TherapistNameStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:therapist_names,name'],
            'redirect' => ['nullable', 'string'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ];
    }
}
