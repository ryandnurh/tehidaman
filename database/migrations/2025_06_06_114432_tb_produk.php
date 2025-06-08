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
        Schema::create('tb_produk', function (Blueprint $table) {
            $table->string('id_produk', 10)->primary();
            $table->string('id_kategori', 10);
            $table->string('gambar_produk')->nullable();
            $table->string('nama_produk', 50);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 10, 2);
            $table->integer('jumlah_terjual')->default(0);
            $table->timestamps();

            $table->foreign('id_kategori')->references('id_kategori')->on('tb_kategori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_produk', function (Blueprint $table) {
            $table->dropForeign(['id_kategori']);
        });
        Schema::dropIfExists('tb_produk');
    }
};
