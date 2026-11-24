<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddMediaToCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media_id' => ['required', 'integer', 'exists:media,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'media_id.required' => 'ID media wajib diisi.',
            'media_id.exists' => 'Media yang dipilih tidak ditemukan dalam sistem.',
        ];
    }
}
