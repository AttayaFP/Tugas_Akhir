<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    const STATUS_PESANAN_PENDING = 'Pending';
    const STATUS_PESANAN_PROSES = 'Proses';
    const STATUS_PESANAN_DIKIRIM = 'Dikirim';
    const STATUS_PESANAN_SAMPAI = 'Sampai';
    const STATUS_PESANAN_SELESAI = 'Selesai';

    // Status yang bisa diubah oleh Admin
    const ADMIN_ALLOWED_STATUS = [
        self::STATUS_PESANAN_PENDING,
        self::STATUS_PESANAN_PROSES,
        self::STATUS_PESANAN_DIKIRIM,
        self::STATUS_PESANAN_SAMPAI,
    ];

    // Status yang bisa diubah oleh Pelanggan
    const PELANGGAN_ALLOWED_STATUS = [
        self::STATUS_PESANAN_SELESAI,
    ];

    const STATUS_PEMBAYARAN_BELUM_LUNAS = 'Belum Lunas';
    const STATUS_PEMBAYARAN_DP = 'DP';
    const STATUS_PEMBAYARAN_LUNAS = 'Lunas';

    protected $casts = [
        'tanggal_pesan' => 'datetime',
        'total_harga' => 'decimal:2',
        'uang_muka' => 'decimal:2',
    ];

    protected $fillable = [
        'no_nota',
        'id_pelanggan',
        'id_layanan',
        'tanggal_pesan',
        'jumlah',
        'total_harga',
        'status_pesanan',
        'bukti_pembayaran',
        'foto_desain',
        'metode_pembayaran',
        'keterangan',
        'uang_muka',
        'status_pembayaran',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'id_layanan');
    }

    public static function getMinDPPercentage($kategori)
    {
        return match ($kategori) {
            'Regular' => 0.5,
            'Member' => 0.3,
            'VIP' => 0,
            default => 0.5,
        };
    }
}
