<?php

// Namespace model
namespace App\Models;

// Class dasar Eloquent Model
use Illuminate\Database\Eloquent\Model;

/**
 * Model Transaction
 * Merepresentasikan tabel: stock_transactions
 */
class Transaction extends Model
{
    /**
     * WAJIB didefinisikan karena
     * nama model = Transaction
     * nama tabel = stock_transactions
     * (tidak mengikuti plural default Laravel: transactions)
     */
    protected $table = 'stock_transactions';

    /**
     * Field yang boleh diisi menggunakan mass assignment
     * (create / update)
     */
    protected $fillable = [
        'product_id', // ID produk yang ditransaksikan
        'user_id',    // User yang melakukan transaksi
        'type',       // Jenis transaksi: in / out
        'quantity',   // Jumlah barang
        'nosur',   // Jumlah barang
        'notes',      // Catatan tambahan (opsional)
        'date',       // Tanggal transaksi
    ];

    /**
     * Relasi: Transaction milik satu Product
     * (Many to One)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi: Transaction milik satu User
     * (Many to One)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
