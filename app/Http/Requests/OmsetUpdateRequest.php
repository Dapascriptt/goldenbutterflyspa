<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OmsetUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keterangan' => ['nullable', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:0'],
        ];
    }
}
