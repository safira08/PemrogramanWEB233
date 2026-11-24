<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AddMediaToCollectionRequest
 * 
 * Form Request untuk memvalidasi input saat user menambahkan media ke koleksi pribadinya.
 */
class AddMediaToCollectionRequest extends FormRequest
{
    /**
     * Otorisasi request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi penambahan media ke koleksi pengguna.
     * 
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // media_id wajib ada dan harus terdaftar di tabel media
            'media_id' => ['required', 'integer', 'exists:media,id'],

            // Catatan pribadi terhadap media dalam koleksi bersifat opsional
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Pesan error validasi.
     */
    public function messages(): array
    {
        return [
            'media_id.required' => 'ID media yang ingin ditambahkan ke koleksi wajib disertakan.',
            'media_id.exists' => 'Media yang dipilih tidak ditemukan dalam sistem FlipPlay.',
        ];
    }
}
