<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TherapistUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->input('redirect') === 'summary') {
            return [
                'redirect' => ['nullable', 'string'],
                'tanggal' => ['required', 'date'],
                'nama' => ['required', 'string', 'max:255'],
                'traditional' => ['nullable', 'integer', 'min:0'],
                'fullbody' => ['nullable', 'integer', 'min:0'],
                'butterfly' => ['nullable', 'integer', 'min:0'],
                'extra_time' => ['nullable', 'integer', 'min:0'],
                'room' => ['nullable', 'string', 'max:255'],
            ];
        }

        return [
            'tanggal' => ['required', 'date'],
            'waktu' => ['nullable', 'date_format:H:i'],
            'nama' => ['required', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:255'],
            'extra_time' => ['nullable', 'integer', 'min:0'],
            'traditional' => ['nullable', 'integer', 'min:0'],
            'fullbody' => ['nullable', 'integer', 'min:0'],
            'butterfly' => ['nullable', 'integer', 'min:0'],
            'shockwave' => ['nullable'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount_nominal' => ['nullable', 'integer', 'min:0'],
            'room_charge' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
