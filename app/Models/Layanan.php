<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'nama_layanan',
        'satuan',
        'harga_per_satuan',
        'deskripsi',
        'minimal_order',
        'estimasi_waktu',
        'foto',
    ];
}
