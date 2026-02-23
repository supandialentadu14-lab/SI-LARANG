<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpdSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_opd',
        'alamat_opd',
        'kepala_nama', 'kepala_pangkat', 'kepala_jabatan', 'kepala_nip',
        'pengurus_nama', 'pengurus_pangkat', 'pengurus_jabatan', 'pengurus_nip',
        'pengguna_nama', 'pengguna_pangkat', 'pengguna_jabatan', 'pengguna_nip',
    ];
}
