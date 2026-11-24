<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMediaToCollectionRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Class UserCollectionController
 * 
 * Menangani custom action di luar CRUD standar untuk relasi antartabel:
 * 1. index   : Menampilkan daftar koleksi media milik seorang pengguna
 * 2. store   : Menambahkan media ke dalam koleksi pengguna (attach) dengan proteksi data duplikat (HTTP 409)
 * 3. destroy : Menghapus media dari koleksi pengguna (detach)
 */
class UserCollectionController extends Controller
{
    /**
     * Menampilkan seluruh koleksi media yang dimiliki oleh pengguna tertentu.
     * Menerapkan Eager Loading load('media') agar data media langsung terambil secara efisien.
     * 
     * HTTP Status: 200 OK
     */
    public function index(User $user): JsonResponse
    {
        // Eager load relasi media milik user
        $user->load('media');

        return response()->json([
            'message' => "Koleksi media milik pengguna '{$user->name}' berhasil dimuat.",
            'user' => new UserResource($user),
            'collections' => MediaResource::collection($user->media),
        ], 200);
    }

    /**
     * Custom Action: Menambahkan media ke dalam koleksi pribadi pengguna.
     * - Menggunakan Database Transaction untuk menjaga konsistensi data (race condition).
     * - Memeriksa duplikasi media: mengembalikan status HTTP 409 (Conflict) jika media sudah ada di koleksi.
     * - Menggunakan method attach() untuk menambahkan baris baru ke tabel pivot 'media_user'.
     * 
     * HTTP Status: 201 Created (jika sukses) / 409 Conflict (jika duplikat)
     */
    public function store(AddMediaToCollectionRequest $request, User $user): JsonResponse
    {
        $mediaId = $request->validated('media_id');
        $notes = $request->validated('notes');

        // Membungkus proses dengan database transaction untuk menjamin data integrity
        return DB::transaction(function () use ($user, $mediaId, $notes) {
            // Pengecekan data duplikat (Error Handling Dasar Modul 4 hal 26)
            // Jika media sudah ada di koleksi, tolak dengan status 409 Conflict
            if ($user->media()->where('media_id', $mediaId)->exists()) {
                return response()->json([
                    'message' => 'Media sudah ada di koleksi',
                    'errors' => [
                        'media_id' => ['Media ini sudah ada di dalam koleksi Anda.']
                    ]
                ], 409);
            }

            // Hubungkan media ke pengguna pada tabel pivot menggunakan attach()
            $user->media()->attach($mediaId, [
                'notes' => $notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Ambil model media yang baru ditambahkan untuk ditampilkan di respons
            $media = Media::findOrFail($mediaId);

            // Berikan respons sukses dengan HTTP Status 201 Created
            return response()->json([
                'message' => "Media '{$media->title}' berhasil ditambahkan ke dalam koleksi {$user->name}.",
                'data' => [
                    'user_id' => $user->id,
                    'media' => new MediaResource($media),
                    'notes' => $notes,
                ]
            ], 201);
        });
    }

    /**
     * Custom Action: Menghapus media dari koleksi pengguna.
     * - Memvalidasi apakah media memang ada di dalam koleksi pengguna (mengembalikan 404 jika tidak ditemukan).
     * - Menggunakan method detach() untuk menghapus relasi pada tabel pivot 'media_user'.
     * 
     * HTTP Status: 200 OK
     */
    public function destroy(User $user, Media $media): JsonResponse
    {
        // Periksa apakah media tersebut benar-benar ada di koleksi pengguna
        if (! $user->media()->where('media_id', $media->id)->exists()) {
            return response()->json([
                'message' => "Gagal: Media '{$media->title}' tidak ditemukan di dalam koleksi {$user->name}.",
            ], 404);
        }

        // Putus hubungan relasi many-to-many di tabel pivot menggunakan detach()
        $user->media()->detach($media->id);

        // Berikan respons konfirmasi penghapusan berhasil (HTTP 200 OK)
        return response()->json([
            'message' => "Media '{$media->title}' berhasil dihapus dari koleksi {$user->name}.",
        ], 200);
    }
}
