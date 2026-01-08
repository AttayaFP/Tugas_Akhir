<?php

namespace App\Jobs;

use App\Models\Pemesanan;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckOrderDeliveryStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pemesanan;

    /**
     * Create a new job instance.
     */
    public function __construct(Pemesanan $pemesanan)
    {
        $this->pemesanan = $pemesanan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Refresh data dari database untuk memastikan status terbaru
        $pemesanan = $this->pemesanan->refresh();

        // Jika status masih 'Sampai' (belum diubah jadi 'Selesai' oleh pelanggan)
        if ($pemesanan->status_pesanan === Pemesanan::STATUS_PESANAN_SAMPAI) {
            $pelanggan = $pemesanan->pelanggan;

            if ($pelanggan && $pelanggan->fcm_token) {
                Log::info("Mengirim follow-up notifikasi (5 menit) untuk Nota: {$pemesanan->no_nota}");

                FcmService::sendNotification(
                    $pelanggan->fcm_token,
                    "Konfirmasi Penerimaan 📋",
                    "Hai Kak! Pesanan #{$pemesanan->no_nota} sudah diterima dengan baik? Jangan lupa tekan 'Selesai' untuk mengkonfirmasi ya! 😊",
                    [
                        'no_nota' => (string) $pemesanan->no_nota,
                        'type' => 'DELIVERY_CHECK',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                    ]
                );
            }
        }
    }
}
