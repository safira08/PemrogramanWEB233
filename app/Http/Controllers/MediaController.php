<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class MediaController
 * 
 * Menangani operasi CRUD standar untuk entitas Media:
 * - index   : Menampilkan semua media (dengan query filter pencarian)
 * - store   : Menambahkan media baru (status code 201)
 * - show    : Menampilkan detail satu media beserta relasi pengguna (eager loading)
 * - update  : Memperbarui data media (status code 200)
 * - destroy : Menghapus media dari sistem (status code 200)
 */
class MediaController extends Controller
{
    /**
     * Menampilkan daftar seluruh media yang tersedia di FlipPlay.
     * Mendukung fitur pencarian judul / genre melalui query string (contoh: /api/media?title=Attack atau ?search=One).
     * 
     * HTTP Status: 200 OK
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Inisialisasi query Eloquent
        $query = Media::query();

        // Fitur Query Filter: Pencarian umum berdasarkan kata kunci pada judul, genre, atau deskripsi
        if ($request->filled('search')) {
            $keyword = $request->query('search');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('genre', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Fitur Query Filter spesifik berdasarkan judul
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->query('title') . '%');
        }

        // Fitur Query Filter berdasarkan jenis media (anime, drama, manga, light novel)
        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        // Fitur Query Filter berdasarkan genre media
        if ($request->filled('genre')) {
            $query->where('genre', 'like', '%' . $request->query('genre') . '%');
        }

        // Ambil data terbaru dari database
        $mediaList = $query->latest()->get();

        // Mengembalikan kumpulan data dengan format konsisten dari API Resource
        return MediaResource::collection($mediaList);
    }

    /**
     * Menambahkan media baru ke dalam sistem FlipPlay.
     * Menggunakan StoreMediaRequest untuk memastikan data yang dikirim valid.
     * 
     * HTTP Status: 201 Created (sesuai best practice RESTful API)
     */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        // Simpan data media baru yang sudah tervalidasi
        $media = Media::create($request->validated());

        // Kembalikan response resource dengan status HTTP 201 (Created)
        return (new MediaResource($media))
            ->additional(['message' => 'Media baru berhasil ditambahkan ke dalam katalog FlipPlay.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Menampilkan detail satu media beserta pengguna yang memasukkannya ke koleksi.
     * Menerapkan Route Model Binding dan Eager Loading relasi 'users' (whenLoaded).
     * 
     * HTTP Status: 200 OK
     */
    public function show(Media $media): MediaResource
    {
        // Eager load relasi Many-to-Many 'users' untuk menghindari N+1 query problem
        $media->load('users');

        // Kembalikan data media lengkap dengan pengguna yang mengoleksinya
        return new MediaResource($media);
    }

    /**
     * Memperbarui data media berdasarkan ID.
     * Menggunakan UpdateMediaRequest untuk validasi input pembaruan.
     * 
     * HTTP Status: 200 OK
     */
    public function update(UpdateMediaRequest $request, Media $media): JsonResponse
    {
        // Perbarui atribut media dengan data yang telah lolos validasi
        $media->update($request->validated());

        // Kembalikan response resource dengan status HTTP 200 (OK)
        return (new MediaResource($media))
            ->additional(['message' => "Data media '{$media->title}' berhasil diperbarui."])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Menghapus media dari katalog FlipPlay berdasarkan ID.
     * 
     * HTTP Status: 200 OK
     */
    public function destroy(Media $media): JsonResponse
    {
        // Simpan judul untuk keperluan pesan konfirmasi
        $mediaTitle = $media->title;

        // Hapus rekaman data media dari database
        $media->delete();

        // Kembalikan konfirmasi penghapusan dengan status HTTP 200
        return response()->json([
            'message' => "Media '{$mediaTitle}' berhasil dihapus secara permanen dari sistem FlipPlay.",
        ], 200);
    }
}
