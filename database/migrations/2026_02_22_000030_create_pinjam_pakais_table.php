<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjam_pakais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nomor')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('tempat')->nullable();
            $table->text('pembuka')->nullable();
            $table->string('pihak_pertama_nama')->nullable();
            $table->string('pihak_pertama_nip')->nullable();
            $table->string('pihak_pertama_jabatan')->nullable();
            $table->string('pihak_kedua_nama')->nullable();
            $table->string('pihak_kedua_nip')->nullable();
            $table->string('pihak_kedua_jabatan')->nullable();
            $table->text('ketentuan')->nullable();
            $table->string('berlaku_hingga')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjam_pakais');
    }
};

