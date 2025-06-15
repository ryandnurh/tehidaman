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
        Schema::create('tb_promo', function(Blueprint $table){
            $table->string('id_promo', 50)->primary();
            $table->string('nama_promo', 100);
            $table->string('kode_promo', 50)->unique()->nullable();
            $table->text('deskripsi');
            $table->string('gambar')->nullable();
            $table->enum('jenis',['persentase', 'nominal'])->default('persentase');
            $table->decimal('nilai_diskon', 12, 2)->nullable();
            $table->decimal('minimal_pembelian', 12, 2)->default(0);
            $table->decimal('maksimal_diskon', 12, 2)->nullable();
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_berakhir');
            $table->integer('jumlah_penggunaan')->default(0);
            $table->integer('kuota_promo')->nullable();
            $table->enum('status', ['aktif', 'nonaktif','kadaluarsa','habis'])->default('aktif');
            $table->text('syarat_ketentuan')->nullable();
            $table->boolean('target_semua_user')->default(true);
            $table->boolean('target_user_baru')->default(false);
            $table->boolean('target_semua_produk')->default(true);
            $table->string('id_kategori_target')->nullable();
            $table->string('id_produk_target')->nullable();
            $table->string('deskripsi_singkat')->nullable();
            $table->timestamps();

            $table->foreign('id_kategori_target')->references('id_kategori')->on('tb_kategori')->onDelete('set null');
            $table->foreign('id_produk_target')->references('id_produk')->on('tb_produk')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_promo', function (Blueprint $table) {
            $table->dropForeign(['id_kategori_target']);
            $table->dropForeign(['id_produk_target']);
        });
        Schema::dropIfExists('tb_promo');
    }
};
