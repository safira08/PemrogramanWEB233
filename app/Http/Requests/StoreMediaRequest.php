<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreMediaRequest
 * 
 * Form Request untuk memvalidasi input saat menambahkan media baru (POST /api/media).
 * Menjamin integritas data sebelum masuk ke database server.
 */
class StoreMediaRequest extends FormRequest
{
    /**
     * Tentukan apakah request ini diizinkan untuk dieksekusi.
     * Mengembalikan true agar semua klien yang valid dapat mengakses.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi data input.
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Judul media wajib diisi, berupa string, maksimal 255 karakter, dan tidak boleh duplikat di tabel media
            'title' => ['required', 'string', 'max:255', 'unique:media,title'],

            // Jenis media wajib diisi dan dibatasi jenis yang valid di FlipPlay
            'type' => ['required', 'string', 'in:movie,series,music,podcast,game'],

            // Genre media wajib diisi, maksimal 100 karakter
            'genre' => ['required', 'string', 'max:100'],

            // Tahun rilis opsional, jika diisi harus angka tahun antara 1800 - 2100
            'release_year' => ['nullable', 'integer', 'min:1800', 'max:2100'],

            // Durasi opsional dalam satuan menit, minimal 1 menit
            'duration' => ['nullable', 'integer', 'min:1'],

            // Deskripsi media bersifat opsional berupa teks
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Kustomisasi pesan error validasi dalam bahasa Indonesia yang ramah pengguna.
     * Menghasilkan respons HTTP 422 Unprocessable Content jika validasi gagal.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media ini sudah terdaftar di FlipPlay, silakan gunakan judul lain.',
            'type.required' => 'Jenis media wajib dipilih.',
            'type.in' => 'Jenis media harus salah satu dari: movie, series, music, podcast, game.',
            'genre.required' => 'Genre media wajib diisi.',
            'release_year.integer' => 'Tahun rilis harus berupa angka tahun.',
            'duration.integer' => 'Durasi harus berupa angka bulat dalam menit.',
        ];
    }
}
