<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota_id',
        'name',
        'qty',
        'unit',
        'price',
        'total',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'integer',
        'total' => 'integer',
    ];

    public function nota(): BelongsTo
    {
        return $this->belongsTo(NotaPesanan::class, 'nota_id');
    }
}
