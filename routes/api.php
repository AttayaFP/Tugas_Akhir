<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/pelanggan/login', [\App\Http\Controllers\Api\PelangganController::class, 'login']);
Route::post('/pelanggan/register', [\App\Http\Controllers\Api\PelangganController::class, 'tambah']);
Route::post('/pelanggan/reset-password', [\App\Http\Controllers\Api\PelangganController::class, 'resetPassword']);
Route::post('/fcm-token', [\App\Http\Controllers\Api\NotificationController::class, 'updateToken']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/pelanggan/logout', [\App\Http\Controllers\Api\PelangganController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'getUser']);

    Route::controller(\App\Http\Controllers\Api\PelangganController::class)->prefix('pelanggan')->group(function () {
        Route::get('/', 'tampil');
        Route::post('/', 'tambah');
        Route::get('/{id}', 'detail');
        Route::put('/{id}', 'ubah');
        Route::delete('/{id}', 'hapus');
    });

    Route::controller(\App\Http\Controllers\Api\LayananController::class)->prefix('layanan')->group(function () {
        Route::get('/', 'tampil');
        Route::post('/', 'tambah');
        Route::get('/{id}', 'detail');
        Route::put('/{id}', 'ubah');
        Route::delete('/{id}', 'hapus');
    });

    Route::controller(\App\Http\Controllers\Api\PemesananController::class)->prefix('pemesanan')->group(function () {
        Route::get('/', 'tampil');
        Route::post('/', 'tambah');
        Route::get('/{id}', 'detail');
        Route::put('/{id}', 'ubah');
        Route::delete('/{id}', 'hapus');
        Route::get('/{id}/faktur', 'faktur');
    });
});
