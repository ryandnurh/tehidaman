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
        Schema::create('tb_favorit', function (Blueprint $table) {
            $table->string('id_user');
            $table->string('id_produk');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_user')->references('id_user')->on('tb_users')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('tb_produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_favorit', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_produk']);
        });
        Schema::dropIfExists('tb_favorit');
    }
};
