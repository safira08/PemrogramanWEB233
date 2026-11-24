<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserCollectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FlipPlay REST API Routes (Modul 4)
|--------------------------------------------------------------------------
| Seluruh endpoint didaftarkan di file ini dan dapat diverifikasi melalui:
| `php artisan route:list`
|
| Konvensi HTTP Method & Endpoint:
| - GET    /api/media                  : Menampilkan seluruh media (support ?search=...)
| - POST   /api/media                  : Menambahkan media baru (Status 201)
| - GET    /api/media/{media}          : Menampilkan detail satu media & pengguna kolektornya
| - PUT    /api/media/{media}          : Memperbarui media (Status 200)
| - DELETE /api/media/{media}          : Menghapus media dari katalog (Status 200)
|
| Custom Action Relasi antartabel:
| - GET    /api/users/{user}/collections         : Melihat koleksi media pengguna
| - POST   /api/users/{user}/collections         : Menambahkan media ke koleksi (attach)
| - DELETE /api/users/{user}/collections/{media} : Menghapus media dari koleksi (detach)
*/

// 1. Endpoint CRUD Standar untuk Entitas Media
// Menggunakan parameters(['media' => 'media']) agar binding route model {media} sesuai tipe-hint parameter MediaController
Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);

// 2. Endpoint Pengguna untuk memfasilitasi pengujian API
Route::apiResource('users', UserController::class)->only(['index', 'store', 'show']);

// 3. Endpoint Custom Action di Luar CRUD Standar (Mengubah Relasi Many-to-Many Pengguna & Media)
// a. Menampilkan koleksi media milik pengguna tertentu
Route::get('users/{user}/collections', [UserCollectionController::class, 'index'])
    ->name('users.collections.index');

// b. Menambahkan media ke dalam koleksi pribadi pengguna (attach)
Route::post('users/{user}/collections', [UserCollectionController::class, 'store'])
    ->name('users.collections.store');

// c. Menghapus media dari koleksi pengguna (detach)
Route::delete('users/{user}/collections/{media}', [UserCollectionController::class, 'destroy'])
    ->name('users.collections.destroy');
