<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bap_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bap_id')->constrained('bap_pemeriksaans')->onDelete('cascade');
            $table->string('nama');
            $table->integer('kuantitas');
            $table->string('satuan')->nullable();
            $table->integer('harga')->default(0);
            $table->integer('jumlah')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bap_items');
    }
};
