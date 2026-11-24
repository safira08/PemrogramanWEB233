<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:media,title'],
            'type' => ['required', 'string', 'in:movie,series,music,podcast,game'],
            'genre' => ['required', 'string', 'max:100'],
            'release_year' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media ini sudah terdaftar dalam sistem.',
            'type.required' => 'Jenis media wajib diisi.',
            'type.in' => 'Jenis media harus salah satu dari: movie, series, music, podcast, game.',
            'genre.required' => 'Genre media wajib diisi.',
            'release_year.integer' => 'Tahun rilis harus berupa angka tahun.',
            'duration.integer' => 'Durasi harus berupa angka bulat (menit).',
        ];
    }
}
