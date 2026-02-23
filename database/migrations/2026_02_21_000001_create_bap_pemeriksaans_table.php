<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bap_pemeriksaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nomor')->index();
            $table->date('tanggal');
            $table->string('tempat')->nullable();
            $table->string('nota_nomor')->nullable();
            $table->date('nota_tanggal')->nullable();
            $table->string('belanja')->nullable();
            $table->string('penyedia_toko')->nullable();
            $table->string('penyedia_alamat')->nullable();
            $table->string('ppk_nama')->nullable();
            $table->string('ppk_nip')->nullable();
            $table->string('ppk_alamat')->nullable();
            $table->integer('total')->default(0);
            $table->string('terbilang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bap_pemeriksaans');
    }
};
