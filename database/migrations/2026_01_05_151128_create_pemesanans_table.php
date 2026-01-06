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
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota')->unique();
            $table->foreignId('id_pelanggan')->constrained('pelanggans')->onDelete('cascade');
            $table->foreignId('id_layanan')->constrained('layanans')->onDelete('cascade');
            $table->foreignId('id_karyawan')->constrained('users')->onDelete('cascade');
            $table->dateTime('tanggal_pesan');
            $table->integer('jumlah');
            $table->decimal('total_harga', 15, 2);
            $table->string('status_pesanan')->default('Pending'); // Pending, Proses, Selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
