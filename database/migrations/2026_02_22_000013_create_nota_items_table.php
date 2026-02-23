<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_id')->constrained('nota_pesanans')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->integer('qty')->default(0);
            $table->string('unit')->nullable();
            $table->bigInteger('price')->default(0);
            $table->bigInteger('total')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_items');
    }
};
