<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Mendukung pencarian / filter berdasarkan judul (query filter), jenis, dan genre.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Media::query();

        // Mencari media berdasarkan judul (query filter pada endpoint index)
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->query('title') . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('genre')) {
            $query->where('genre', 'like', '%' . $request->query('genre') . '%');
        }

        $media = $query->latest()->get();

        return MediaResource::collection($media);
    }

    /**
     * Store a newly created resource in storage.
     * Menggunakan StoreMediaRequest untuk validasi.
     */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->additional(['message' => 'Media berhasil ditambahkan ke FlipPlay.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     * Menampilkan detail satu media beserta pengguna yang memasukkannya ke koleksi.
     */
    public function show(Media $media): MediaResource
    {
        $media->load('users');

        return new MediaResource($media);
    }

    /**
     * Update the specified resource in storage.
     * Menggunakan UpdateMediaRequest untuk validasi.
     */
    public function update(UpdateMediaRequest $request, Media $media): JsonResponse
    {
        $media->update($request->validated());

        return (new MediaResource($media))
            ->additional(['message' => 'Data media berhasil diperbarui.'])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media): JsonResponse
    {
        $title = $media->title;
        $media->delete();

        return response()->json([
            'message' => "Media '{$title}' berhasil dihapus dari sistem FlipPlay.",
        ], 200);
    }
}
