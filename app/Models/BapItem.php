<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BapItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bap_id',
        'nama',
        'kuantitas',
        'satuan',
        'harga',
        'jumlah',
    ];

    public function bap(): BelongsTo
    {
        return $this->belongsTo(BapPemeriksaan::class, 'bap_id');
    }
}
