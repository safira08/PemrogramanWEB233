<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserCollectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FlipPlay REST API Routes
|--------------------------------------------------------------------------
*/

// Endpoint CRUD Media
Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);

// Endpoint Pengguna (untuk keperluan testing & melihat relasi pengguna)
Route::apiResource('users', UserController::class)->only(['index', 'store', 'show']);

// Endpoint Koleksi Pengguna (Custom Action relasi antartabel)
// 1. Menampilkan koleksi media seorang pengguna
Route::get('users/{user}/collections', [UserCollectionController::class, 'index'])->name('users.collections.index');

// 2. Menambahkan media ke koleksi pengguna
Route::post('users/{user}/collections', [UserCollectionController::class, 'store'])->name('users.collections.store');

// 3. Menghapus media dari koleksi pengguna
Route::delete('users/{user}/collections/{media}', [UserCollectionController::class, 'destroy'])->name('users.collections.destroy');
