<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMediaToCollectionRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserCollectionController extends Controller
{
    /**
     * Menampilkan koleksi media yang dimiliki seorang pengguna.
     */
    public function index(User $user): JsonResponse
    {
        $user->load('media');

        return response()->json([
            'message' => "Daftar koleksi media milik pengguna '{$user->name}' berhasil dimuat.",
            'user' => new UserResource($user),
            'collections' => MediaResource::collection($user->media),
        ], 200);
    }

    /**
     * Menambahkan media ke dalam koleksi pribadi pengguna (custom action relasi antartabel).
     */
    public function store(AddMediaToCollectionRequest $request, User $user): JsonResponse
    {
        $mediaId = $request->validated('media_id');
        $notes = $request->validated('notes');

        // Mencegah duplikasi media di koleksi pengguna yang sama
        if ($user->media()->where('media.id', $mediaId)->exists()) {
            return response()->json([
                'message' => 'Gagal: Media ini sudah ada di dalam koleksi Anda.',
                'errors' => [
                    'media_id' => ['Media ini sudah terdaftar di koleksi pengguna tersebut.']
                ]
            ], 422);
        }

        $user->media()->attach($mediaId, [
            'notes' => $notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $addedMedia = Media::findOrFail($mediaId);

        return response()->json([
            'message' => "Media '{$addedMedia->title}' berhasil ditambahkan ke dalam koleksi {$user->name}.",
            'data' => [
                'user_id' => $user->id,
                'media' => new MediaResource($addedMedia),
                'notes' => $notes,
            ]
        ], 201);
    }

    /**
     * Menghapus media dari koleksi pengguna (custom action relasi antartabel).
     */
    public function destroy(User $user, Media $media): JsonResponse
    {
        // Validasi apakah media benar-benar ada di koleksi pengguna
        if (! $user->media()->where('media.id', $media->id)->exists()) {
            return response()->json([
                'message' => "Gagal: Media '{$media->title}' tidak ditemukan di dalam koleksi {$user->name}.",
            ], 404);
        }

        $user->media()->detach($media->id);

        return response()->json([
            'message' => "Media '{$media->title}' berhasil dihapus dari koleksi {$user->name}.",
        ], 200);
    }
}
