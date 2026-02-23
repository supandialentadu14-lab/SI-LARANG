<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjam_pakai_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjam_id')->constrained('pinjam_pakais')->onDelete('cascade');
            $table->string('nama')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->string('identitas')->nullable();
            $table->string('tahun')->nullable();
            $table->string('kondisi', 50)->nullable();
            $table->integer('jumlah')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjam_pakai_items');
    }
};

