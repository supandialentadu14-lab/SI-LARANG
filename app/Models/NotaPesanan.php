<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotaPesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor',
        'tanggal',
        'kegiatan',
        'sub_kegiatan',
        'rekening',
        'tahun',
        'belanja',
        'penyedia_toko',
        'penyedia_pemilik',
        'penyedia_alamat',
        'pejabat_nama',
        'pejabat_nip',
        'pptk_nama',
        'pptk_nip',
        'ppk_nama',
        'ppk_nip',
        'bendahara_nama',
        'bendahara_nip',
        'total',
        'terbilang',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tahun' => 'integer',
        'total' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(NotaItem::class, 'nota_id');
    }
}
