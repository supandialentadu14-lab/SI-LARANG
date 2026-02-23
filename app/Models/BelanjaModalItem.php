<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BelanjaModalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'belanja_modal_id',
        'nama_kegiatan',
        'pekerjaan',
        'nilai_kontrak',
        'tanggal_mulai',
        'tanggal_akhir',
        'uang_muka',
        'termin1',
        'termin2',
        'termin3',
        'termin4',
        'total',
        'status',
    ];

    public function belanjaModal()
    {
        return $this->belongsTo(BelanjaModal::class);
    }
}

