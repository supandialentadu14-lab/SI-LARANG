<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BapPemeriksaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor',
        'tanggal',
        'tempat',
        'nota_nomor',
        'nota_tanggal',
        'belanja',
        'penyedia_toko',
        'penyedia_alamat',
        'ppk_nama',
        'ppk_nip',
        'ppk_alamat',
        'total',
        'terbilang',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nota_tanggal' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BapItem::class, 'bap_id');
    }
}
