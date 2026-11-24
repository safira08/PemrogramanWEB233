<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserController
 * 
 * Controller pembantu untuk mengelola data Pengguna di FlipPlay,
 * memfasilitasi pengujian API koleksi pengguna pada Postman dan unit testing.
 */
class UserController extends Controller
{
    /**
     * Menampilkan seluruh daftar pengguna beserta koleksi medianya.
     */
    public function index(): AnonymousResourceCollection
    {
        // Eager load relasi media untuk mencegah N+1 problem
        return UserResource::collection(User::with('media')->latest()->get());
    }

    /**
     * Mendaftarkan pengguna baru ke FlipPlay.
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi input pendaftaran pengguna
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Buat user dengan password terenkripsi Hash
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Mengembalikan data user baru dengan status HTTP 201 Created
        return (new UserResource($user))
            ->additional(['message' => 'Pengguna baru berhasil didaftarkan.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Menampilkan detail satu pengguna beserta daftar koleksi medianya.
     */
    public function show(User $user): UserResource
    {
        $user->load('media');

        return new UserResource($user);
    }
}
