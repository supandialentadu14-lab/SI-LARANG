<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'opd_nama',
        'opd_alamat',
        'ppk_nama',
        'ppk_nip',
        'ppk_alamat',
        'pejabat_nama',
        'pejabat_nip',
        'pptk_nama',
        'pptk_nip',
        'pengurus_barang_nama',
        'pengurus_barang_nip',
        'pengurus_pengguna_nama',
        'pengurus_pengguna_nip',
        'bendahara_nama',
        'bendahara_nip',
        'penyedia_toko',
        'penyedia_pemilik',
        'penyedia_alamat',
    ];
}
