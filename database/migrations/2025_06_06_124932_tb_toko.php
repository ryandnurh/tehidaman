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
        Schema::create('tb_toko', function (Blueprint $table) {
            $table->string('id_toko', 10)->primary();
            $table->string('nama_toko', 50);
            $table->string('alamat_toko', 100)->nullable();
            $table->string('no_hp_toko', 15)->nullable();
            $table->string('email_toko', 50)->unique()->nullable();
            $table->string('foto_toko')->nullable();
            $table->enum('status_toko', ['buka', 'tutup'])->default('buka');
            $table->string('username_admin')->unique();
            $table->string('password_admin');
            $table->decimal('latitude',10,8)->nullable();
            $table->decimal('longitude',11,8)->nullable();
            $table->timestamps();
        });

        Schema::create('tb_produk_toko', function (Blueprint $table) {
            $table->string('id_toko', 10)->nullable();
            $table->string('id_produk', 10);
            $table->integer('stok')->default(0);
            $table->enum('status', ['habis', 'tersedia'])->default('tersedia');
            $table->timestamps();

            $table->primary(['id_toko', 'id_produk']);
            $table->foreign('id_toko')->references('id_toko')->on('tb_toko')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('tb_produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
