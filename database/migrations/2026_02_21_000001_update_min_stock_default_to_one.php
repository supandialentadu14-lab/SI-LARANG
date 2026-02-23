<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE products MODIFY min_stock INT NOT NULL DEFAULT 1');
        DB::table('products')->where('min_stock', 10)->update(['min_stock' => 1]);
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products MODIFY min_stock INT NOT NULL DEFAULT 10');
    }
};
