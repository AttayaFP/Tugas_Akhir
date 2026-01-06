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
        Schema::table('pemesanans', function (Blueprint $table) {
            // Drop foreign key first (convention: table_column_foreign)
            $table->dropForeign(['id_karyawan']);
            $table->dropColumn('id_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->foreignId('id_karyawan')->nullable()->constrained('users')->onDelete('set null');
        });
    }
};
