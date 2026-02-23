<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('opd_settings', function (Blueprint $table) {
            $table->string('alamat_opd')->nullable()->after('nama_opd');
        });
    }

    public function down(): void
    {
        Schema::table('opd_settings', function (Blueprint $table) {
            $table->dropColumn('alamat_opd');
        });
    }
};
