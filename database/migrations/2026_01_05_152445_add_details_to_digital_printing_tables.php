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
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->string('kode_member')->nullable()->after('email');
            $table->string('kategori_pelanggan')->default('Regular')->after('kode_member');
        });

        Schema::table('layanans', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('nama_layanan');
            $table->integer('minimal_order')->default(1)->after('satuan');
            $table->string('estimasi_waktu')->nullable()->after('minimal_order');
        });

        Schema::table('pemesanans', function (Blueprint $table) {
            $table->string('file_desain')->nullable()->after('total_harga');
            $table->text('keterangan')->nullable()->after('file_desain');
            $table->decimal('uang_muka', 15, 2)->default(0)->after('keterangan');
            $table->string('status_pembayaran')->default('Belum Lunas')->after('uang_muka');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn(['kode_member', 'kategori_pelanggan']);
        });

        Schema::table('layanans', function (Blueprint $table) {
            $table->dropColumn(['deskripsi', 'minimal_order', 'estimasi_waktu']);
        });

        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropColumn(['file_desain', 'keterangan', 'uang_muka', 'status_pembayaran']);
        });
    }
};
