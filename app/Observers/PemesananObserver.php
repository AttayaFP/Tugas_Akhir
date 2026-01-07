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
        // Deteksi apakah status pesanan atau status pembayaran berubah
        $statusChanged = $pemesanan->wasChanged('status_pesanan');
        $pembayaranChanged = $pemesanan->wasChanged('status_pembayaran');

        if ($statusChanged || $pembayaranChanged) {
            // Gunakan static variable untuk mencegah duplikasi notifikasi dalam satu request (misal saat save nota yang isinya banyak item)
            static $notifiedNotas = [];
            $noNota = $pemesanan->no_nota;

            if (in_array($noNota, $notifiedNotas)) {
                return;
            }
            $notifiedNotas[] = $noNota;

            $pelanggan = $pemesanan->pelanggan;
            if (!$pelanggan || !$pelanggan->fcm_token) {
                Log::warning("Notifikasi tidak dikirim: Pelanggan tidak ditemukan atau fcm_token kosong untuk Nota: {$noNota}");
                return;
            }

            $status = $pemesanan->status_pesanan;

            // Mapping pesan estetik berdasarkan status
            $statusMessages = [
                Pemesanan::STATUS_PESANAN_PENDING => [
                    'title' => "Pesanan Diterima! 🕒",
                    'body' => "Pesanan #{$noNota} sudah masuk antrean. Tunggu konfirmasi admin ya, Kak! ✨"
                ],
                Pemesanan::STATUS_PESANAN_PROSES => [
                    'title' => "Sedang Dikerjakan ✨",
                    'body' => "Kabar baik! Pesanan #{$noNota} Anda kini dalam tahap pengerjaan. Kami buatkan yang terbaik! 💪"
                ],
                Pemesanan::STATUS_PESANAN_SELESAI => [
                    'title' => "Pesanan Selesai! 🎉",
                    'body' => "Yeay! Pesanan #{$noNota} Kakak sudah jadi. Silakan diambil atau tunggu info pengiriman ya! 🏠"
                ],
                Pemesanan::STATUS_PESANAN_DIAMBIL => [
                    'title' => "Pesanan Dikirim! 🚚",
                    'body' => "Kabar gembira! Pesanan #{$noNota} Kakak sedang dalam perjalanan menuju alamat tujuan. Ditunggu ya! ✨"
                ],
            ];

            $msg = $statusMessages[$status] ?? [
                'title' => "Pembaruan Pesanan! 📦",
                'body' => "Ada update terbaru nih untuk pesanan #{$noNota} Kakak. Cek aplikasinya yuk!"
            ];

            $title = $msg['title'];
            $body = $msg['body'];
            $type = "STATUS_UPDATE";

            // Logika Khusus Penagihan (Payment Reminder)
            $sisaTagihan = $pemesanan->total_harga - $pemesanan->uang_muka;

            if ($pembayaranChanged && $pemesanan->status_pembayaran !== Pemesanan::STATUS_PEMBAYARAN_LUNAS && $sisaTagihan > 0) {
                $title = "Info Pembayaran 💳";
                $formattedSisa = number_format($sisaTagihan, 0, ',', '.');
                $body = "Hai! Sekadar info, untuk pesanan #{$noNota} masih ada sisa tagihan Rp {$formattedSisa}. Segera dilunasi ya, Kak! 😊";
                $type = "PAYMENT_REMINDER";
            }

            Log::info("Mengirim FCM ({$type}) ke Pelanggan {$pelanggan->id} untuk Nota {$noNota}");

            FcmService::sendNotification(
                $pelanggan->fcm_token,
                $title,
                $body,
                [
                    'no_nota' => (string) $noNota,
                    'type' => $type,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]
            );
        }
    }
}
