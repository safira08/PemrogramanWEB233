<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateMediaRequest
 * 
 * Form Request untuk memvalidasi input saat memperbarui media (PUT/PATCH /api/media/{id}).
 * Menggunakan rule 'sometimes' agar mendukung pembaruan sebagian data (PATCH).
 */
class UpdateMediaRequest extends FormRequest
{
    /**
     * Otorisasi request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi data pembaruan media.
     * 
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Ambil objek media atau ID media dari parameter route saat ini
        $media = $this->route('media');
        $mediaId = is_object($media) ? $media->id : $media;

        return [
            // Rule unique mengecualikan ID media saat ini agar tidak dianggap duplikat terhadap dirinya sendiri
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('media', 'title')->ignore($mediaId),
            ],
            'type' => ['sometimes', 'required', 'string', 'in:movie,series,music,podcast,game'],
            'genre' => ['sometimes', 'required', 'string', 'max:100'],
            'release_year' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Pesan error validasi kustom dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media ini sudah terdaftar di sistem FlipPlay.',
            'type.in' => 'Jenis media harus berupa: movie, series, music, podcast, atau game.',
            'genre.required' => 'Genre media wajib diisi.',
            'release_year.integer' => 'Tahun rilis harus berupa angka tahun.',
            'duration.integer' => 'Durasi harus berupa angka bulat dalam menit.',
        ];
    }
}
