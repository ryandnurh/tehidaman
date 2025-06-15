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
        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->string('id_transaksi',50)->primary();
            $table->string('id_user',50);
            $table->string('id_toko',50);
            $table->string('id_alamat', 50)->nullable();
            $table->decimal('total_harga',10,2);
            $table->string('id_promo_terpakai',10)->nullable();
            $table->decimal('diskon',10,2)->nullable();
            $table->decimal('harga_akhir',10,2);
            $table->enum('status',['menunggu pembayaran', 'sedang dibuat', 'sedang diantar', 'selesai', 'gagal']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_user')->references('id_user')->on('tb_users');
            $table->foreign('id_toko')->references('id_toko')->on('tb_toko');
            $table->foreign('id_alamat')->references('id_alamat')->on('tb_alamat');
            $table->foreign('id_promo_terpakai')->references('id_promo')->on('tb_promo');
        });

        Schema::create('tb_detail_transaksi', function (Blueprint $table){
            $table->string('id_transaksi',50);
            $table->string('id_produk',50);
            $table->integer('jumlah');
            $table->decimal('harga_total',10,2);
            $table->timestamps();

            $table->foreign('id_produk')->references('id_produk')->on('tb_produk');
            $table->foreign('id_transaksi')->references('id_transaksi')->on('tb_transaksi');
        });


        Schema::create('tb_pembayaran',function (Blueprint $table){
            $table->string('id_pembayaran',50)->primary();
            $table->string('id_transaksi',50);
            $table->enum('metode pembayaran',['QRIS', 'cod']);
            $table->string('bukti_bayar');
            $table->enum('status',['menunggu pembayaran','terbayar','gagal']);
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id_transaksi')->on('tb_transaksi');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table){
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_toko']);
            $table->dropForeign(['id_alamat']);
            $table->dropForeign(['id_promo_terpakai']);
        });

        Schema::table('tb_detail_transaksi', function (Blueprint $table){
            $table->dropForeign(['id_produk']);
            $table->dropForeign(['id_transaksi']);
        });

        Schema::table('tb_pembayaran', function (Blueprint $table){
            $table->dropForeign(['id_transaksi']);
        });

        Schema::dropIfExist('tb_transaksi');
        Schema::dropIfExist('tb_detail_transaksi');
        Schema::dropIfExists('tb_pembayaran');
    }
};
