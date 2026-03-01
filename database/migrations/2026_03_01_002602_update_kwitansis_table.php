<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kwitansis', function (Blueprint $table) {
            if (!Schema::hasColumn('kwitansis', 'jumlah')) {
                $table->bigInteger('jumlah')->default(0);
            }
            if (!Schema::hasColumn('kwitansis', 'terbilang')) {
                $table->string('terbilang')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'pembayaran_uraian')) {
                $table->text('pembayaran_uraian')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'lokasi_tanggal')) {
                $table->string('lokasi_tanggal')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'opd_nama')) {
                $table->string('opd_nama')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'pptk_nama')) {
                $table->string('pptk_nama')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'pptk_nip')) {
                $table->string('pptk_nip')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'bendahara_nama')) {
                $table->string('bendahara_nama')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'bendahara_nip')) {
                $table->string('bendahara_nip')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'pihak_ketiga_nama')) {
                $table->string('pihak_ketiga_nama')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'pengguna_nama')) {
                $table->string('pengguna_nama')->nullable();
            }
            if (!Schema::hasColumn('kwitansis', 'pengguna_nip')) {
                $table->string('pengguna_nip')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kwitansis', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah',
                'terbilang',
                'pembayaran_uraian',
                'lokasi_tanggal',
                'opd_nama',
                'pptk_nama',
                'pptk_nip',
                'bendahara_nama',
                'bendahara_nip',
                'pihak_ketiga_nama',
                'pengguna_nama',
                'pengguna_nip'
            ]);
        });
    }
};
