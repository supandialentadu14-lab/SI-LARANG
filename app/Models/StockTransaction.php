<?php

// Namespace model
namespace App\Models;

// Trait untuk mendukung factory (seeding & testing)
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Class dasar Model Eloquent
use Illuminate\Database\Eloquent\Model;

// Tipe relasi BelongsTo
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model StockTransaction
 * Merepresentasikan tabel: stock_transactions
 */
class StockTransaction extends Model
{
    // Mengaktifkan fitur factory
    use HasFactory;

    /**
     * Field yang boleh diisi menggunakan mass assignment
     * (create / update)
     */
    protected $fillable = [
        'product_id', // ID produk yang ditransaksikan
        'user_id',    // ID user yang melakukan transaksi
        'type',       // Jenis transaksi: in (masuk) / out (keluar)
        'quantity',   // Jumlah barang
        'nosur',   // Nomor Surat
        'notes',      // Catatan tambahan
        'date',       // Tanggal transaksi
    ];

    /**
     * Casting otomatis tipe data
     * Kolom 'date' akan otomatis menjadi object Carbon
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relasi: Transaksi ini milik satu produk
     * (Many to One)
     */
    public function product(): BelongsTo
    {
        // Foreign key default: product_id
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi: Transaksi ini dilakukan oleh satu user
     * (Many to One)
     */
    public function user(): BelongsTo
    {
        // Foreign key default: user_id
        return $this->belongsTo(User::class);
    }
}
