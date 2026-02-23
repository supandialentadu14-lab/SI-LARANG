<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nomor')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('kegiatan')->nullable();
            $table->string('sub_kegiatan')->nullable();
            $table->string('rekening')->nullable();
            $table->integer('tahun')->nullable();
            $table->string('belanja')->nullable();
            $table->string('penyedia_toko')->nullable();
            $table->string('penyedia_pemilik')->nullable();
            $table->string('penyedia_alamat')->nullable();
            $table->string('pejabat_nama')->nullable();
            $table->string('pejabat_nip')->nullable();
            $table->string('pptk_nama')->nullable();
            $table->string('pptk_nip')->nullable();
            $table->string('ppk_nama')->nullable();
            $table->string('ppk_nip')->nullable();
            $table->string('bendahara_nama')->nullable();
            $table->string('bendahara_nip')->nullable();
            $table->bigInteger('total')->default(0);
            $table->string('terbilang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_pesanans');
    }
};
