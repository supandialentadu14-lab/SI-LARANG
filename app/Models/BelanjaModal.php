<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BelanjaModal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tahun',
        'dataset_id',
        'nilai_total',
    ];

    public function items()
    {
        return $this->hasMany(BelanjaModalItem::class);
    }
}

