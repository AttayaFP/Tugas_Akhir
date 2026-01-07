<?php

namespace App\Observers;

use App\Models\Pemesanan;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class PemesananObserver
{
    /**
     * Handle the Pemesanan "updated" event.
     */
    public function updated(Pemesanan $pemesanan): void
    {
        // Cek apakah status_pesanan berubah
        if ($pemesanan->wasChanged('status_pesanan')) {
            // Karena satu nota bisa punya banyak item, kita hanya kirim notifikasi sekali per nota
            // Kita bisa menggunakan cache atau static variable sederhana untuk turn-around ini dalam satu request
            static $notifiedNotas = [];

            $noNota = $pemesanan->no_nota;
            if (in_array($noNota, $notifiedNotas)) {
                return;
            }
            $notifiedNotas[] = $noNota;

            $pelanggan = $pemesanan->pelanggan;

            if ($pelanggan && $pelanggan->fcm_token) {
                $noNota = $pemesanan->no_nota;
                $statusBaru = $pemesanan->status_pesanan;

                Log::info("Mengirim notifikasi ke pelanggan {$pelanggan->id} untuk nota {$noNota}. Status: {$statusBaru}");

                $success = FcmService::sendNotification(
                    $pelanggan->fcm_token,
                    "Update Status Pesanan",
                    "Pesanan Anda #{$noNota} sekarang berstatus: {$statusBaru}",
                    [
                        'no_nota' => (string) $noNota,
                        'status' => (string) $statusBaru,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK' // Opsional, sesuaikan dengan frontend
                    ]
                );

                if (!$success) {
                    Log::error("Gagal mengirim notifikasi ke pelanggan {$pelanggan->id} untuk nota {$noNota}");
                }
            } else {
                Log::warning("Notifikasi tidak dikirim: Pelanggan tidak ditemukan atau fcm_token kosong untuk Pemesanan ID: {$pemesanan->id}");
            }
        }
    }
}
