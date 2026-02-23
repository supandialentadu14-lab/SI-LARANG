<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opname_id')->constrained('opnames')->onDelete('cascade');
            $table->string('nama')->nullable();
            $table->integer('kuantitas')->default(0);
            $table->string('satuan')->nullable();
            $table->bigInteger('harga')->default(0);
            $table->bigInteger('jumlah')->default(0);
            $table->string('kondisi', 3)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opname_items');
    }
};

