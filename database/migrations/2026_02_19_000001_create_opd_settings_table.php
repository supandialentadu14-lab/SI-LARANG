<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('opd_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_opd')->nullable();
            $table->string('kepala_nama')->nullable();
            $table->string('kepala_pangkat')->nullable();
            $table->string('kepala_jabatan')->nullable();
            $table->string('kepala_nip')->nullable();
            $table->string('pengurus_nama')->nullable();
            $table->string('pengurus_pangkat')->nullable();
            $table->string('pengurus_jabatan')->nullable();
            $table->string('pengurus_nip')->nullable();
            $table->string('pengguna_nama')->nullable();
            $table->string('pengguna_pangkat')->nullable();
            $table->string('pengguna_jabatan')->nullable();
            $table->string('pengguna_nip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opd_settings');
    }
};
