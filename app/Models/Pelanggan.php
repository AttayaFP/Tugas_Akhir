<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Authenticatable
{
    use HasApiTokens, \Illuminate\Database\Eloquent\Factories\HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'no_telepon',
        'alamat',
        'email',
        'kode_member',
        'kategori_pelanggan',
        'fcm_token',
        'password',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
