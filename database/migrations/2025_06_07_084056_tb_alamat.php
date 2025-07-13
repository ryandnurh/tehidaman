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
        Schema::create('tb_alamat',function (Blueprint $table){
            $table->string('id_alamat', 50)->primary();
            $table->string('id_user', 50);
            $table->string('label_alamat', 100);
            $table->string('nama_penerima', 50);
            $table->string('no_hp_penerima', 15);
            $table->text('alamat');
            $table->string('detail_alamat')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['utama', 'tambahan'])->default('tambahan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_alamat', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        Schema::dropIfExists('tb_alamat');
    }
};
