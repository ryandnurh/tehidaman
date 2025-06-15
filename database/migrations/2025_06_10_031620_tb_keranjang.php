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
        Schema::create('tb_keranjang', function (Blueprint $table){
            $table->string('id_keranjang',50)->primary();
            $table->string('id_user',50);
            $table->string('id_toko',50);
            $table->string('id_produk',50);
            $table->integer('jumlah');
            $table->decimal('harga_total',10,2);
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('tb_users')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('tb_produk')->onDelete('cascade');
            $table->foreign('id_toko')->references('id_toko')->on('tb_toko')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_keranjang', function (Blueprint $table){
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_produk']);
        });

        Schema::dropIfExist('tb_keranjang');
    }
};
