<?php

// Menentukan namespace model
namespace App\Models;

// Trait untuk mendukung fitur factory (seeding/testing)
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Class dasar Model Eloquent
use Illuminate\Database\Eloquent\Model;

// Model Product merepresentasikan tabel 'products'
class Product extends Model
{
    // Mengaktifkan fitur factory
    use HasFactory;

    /**
     * Kolom yang boleh diisi menggunakan mass assignment
     * (create() / update())
     */
    protected $fillable = [
        'name',         // Nama produk
        'slug',         // Slug untuk URL
        'sku',          // Kode unik produk
        'price',        // Harga produk
        'stock',        // Stok saat ini (disimpan langsung di tabel)
        'unit',         // Satuan (pcs, box, dll)
        'category_id',  // Relasi ke kategori
        'supplier_id',  // Relasi ke supplier
        'description',  // Deskripsi produk
    ];

    /**
     * Relasi: Produk milik satu kategori
     * (Many to One)
     */
    public function category()
    {
        // belongsTo(ModelTujuan::class)
        // Foreign key default: category_id
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: Produk milik satu supplier
     * (Many to One)
     */
    public function supplier()
    {
        // Foreign key default: supplier_id
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi: Produk memiliki banyak transaksi
     * (One to Many)
     */
    public function transactions()
    {
        // ⚠ Pastikan nama model sesuai.
        // Jika tabelnya stock_transactions, seharusnya:
        // return $this->hasMany(StockTransaction::class);
        return $this->hasMany(Transaction::class);
    }

    /**
     * Accessor: Menghitung stok berdasarkan transaksi
     * Bisa dipanggil dengan:
     * $product->calculated_stock
     */
    public function getCalculatedStockAttribute()
    {
        // Hitung total stok masuk
        $in = $this->transactions()
            ->where('type', 'in')
            ->sum('quantity');

        // Hitung total stok keluar
        $out = $this->transactions()
            ->where('type', 'out')
            ->sum('quantity');

        // Stok akhir = masuk - keluar
        return $in - $out;
    }

    public function getMinStockAttribute($value)
    {
        return max(1, (int)($value ?? 1));
    }
}
