<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('belanja_modal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('belanja_modal_id')->constrained('belanja_modals')->onDelete('cascade');
            $table->string('nama_kegiatan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->bigInteger('nilai_kontrak')->default(0);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->bigInteger('uang_muka')->default(0);
            $table->bigInteger('termin1')->default(0);
            $table->bigInteger('termin2')->default(0);
            $table->bigInteger('termin3')->default(0);
            $table->bigInteger('termin4')->default(0);
            $table->bigInteger('total')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('belanja_modal_items');
    }
};

