<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb']);
Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index', [
            'total_layanan' => \App\Models\Layanan::count(),
            'total_pelanggan' => \App\Models\Pelanggan::count(),
            'total_pemesanan' => \App\Models\Pemesanan::count(),
            'total_pendapatan' => \App\Models\Pemesanan::sum('total_harga'),
            'pending_order' => \App\Models\Pemesanan::where('status_pesanan', 'Pending')->count(),
            'recent_orders' => \App\Models\Pemesanan::with(['pelanggan', 'layanan'])->latest()->take(5)->get()
        ]);
    });

    Route::controller(\App\Http\Controllers\PelangganController::class)->prefix('pelanggan')->group(function () {
        Route::get('/', 'index')->name('pelanggan.index');
        Route::get('/create', 'create')->name('pelanggan.create');
        Route::post('/store', 'store')->name('pelanggan.store');
        Route::get('/{id}/edit', 'edit')->name('pelanggan.edit');
        Route::put('/{id}/update', 'update')->name('pelanggan.update');
        Route::delete('/{id}', 'destroy')->name('pelanggan.destroy');
    });

    Route::controller(\App\Http\Controllers\LayananController::class)->prefix('layanan')->group(function () {
        Route::get('/', 'index')->name('layanan.index');
        Route::get('/create', 'create')->name('layanan.create');
        Route::post('/store', 'store')->name('layanan.store');
        Route::get('/{id}/edit', 'edit')->name('layanan.edit');
        Route::put('/{id}/update', 'update')->name('layanan.update');
        Route::delete('/{id}', 'destroy')->name('layanan.destroy');
    });

    Route::controller(\App\Http\Controllers\PemesananController::class)->prefix('pemesanan')->group(function () {
        Route::get('/', 'index')->name('pemesanan.index');
        Route::get('/{id}', 'show')->name('pemesanan.show');
        Route::post('/{id}/toggle-status', 'toggleStatus')->name('pemesanan.toggle-status');
        Route::post('/{id}/mark-paid', 'markAsPaid')->name('pemesanan.mark-paid');
        Route::delete('/{id}', 'destroy')->name('pemesanan.destroy');
    });
});
