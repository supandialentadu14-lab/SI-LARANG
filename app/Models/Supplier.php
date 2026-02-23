<?php

// Namespace model
namespace App\Models;

// Trait untuk mendukung factory (seeding & testing)
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Class dasar Model Eloquent
use Illuminate\Database\Eloquent\Model;

// Tipe relasi HasMany
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Supplier
 * Merepresentasikan tabel: suppliers
 */
class Supplier extends Model
{
    // Mengaktifkan fitur factory
    use HasFactory;

    /**
     * Field yang boleh diisi menggunakan mass assignment
     * (create / update)
     */
    protected $fillable = [
        'name',    // Nama supplier
        'dir',    // Nama pemilik
        'email',   // Email supplier (opsional)
        'phone',   // Nomor telepon supplier
        'address', // Alamat supplier
    ];

    /**
     * Relasi: Supplier memiliki banyak produk
     * (One to Many)
     */
    public function products(): HasMany
    {
        // Foreign key default: supplier_id pada tabel products
        return $this->hasMany(Product::class);
    }
}
