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
        $uniqueOrders = \App\Models\Pemesanan::select('no_nota', 'total_harga', 'status_pesanan', 'id_pelanggan', 'tanggal_pesan')
            ->get()
            ->unique('no_nota');

        return view('dashboard.index', [
            'total_layanan' => \App\Models\Layanan::count(),
            'total_pelanggan' => \App\Models\Pelanggan::count(),
            'total_pemesanan' => $uniqueOrders->count(),
            'total_pendapatan' => $uniqueOrders->sum('total_harga'),
            'pending_order' => $uniqueOrders->where('status_pesanan', 'Pending')->count(),
            'recent_orders' => \App\Models\Pemesanan::with(['pelanggan', 'layanan'])->latest()->get()->unique('no_nota')->take(5)
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
        Route::get('/create', 'create')->name('pemesanan.create');
        Route::post('/store', 'store')->name('pemesanan.store');
        Route::get('/{id}', 'show')->name('pemesanan.show');
        Route::get('/{id}/edit', 'edit')->name('pemesanan.edit');
        Route::put('/{id}/update', 'update')->name('pemesanan.update');
        Route::post('/{id}/toggle-status', 'toggleStatus')->name('pemesanan.toggle-status');
        Route::post('/{id}/mark-paid', 'markAsPaid')->name('pemesanan.mark-paid');
        Route::get('/{id}/faktur', 'faktur')->name('pemesanan.faktur');
        Route::delete('/{id}', 'destroy')->name('pemesanan.destroy');
    });

    Route::controller(\App\Http\Controllers\LaporanController::class)->prefix('laporan')->group(function () {
        Route::get('/', 'index')->name('laporan.index');
        Route::get('/cetak-pemesanan', 'cetakPemesanan')->name('laporan.cetak-pemesanan');
        Route::get('/cetak-pelanggan', 'cetakPelanggan')->name('laporan.cetak-pelanggan');
        Route::get('/cetak-layanan', 'cetakLayanan')->name('laporan.cetak-layanan');
        Route::get('/cetak-pendapatan', 'cetakPendapatan')->name('laporan.cetak-pendapatan');
    });
});
