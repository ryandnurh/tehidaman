<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProdukToko; // Pastikan namespace model ini benar
use Carbon\Carbon;

class ProdukTokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $now = Carbon::now();

        
        ProdukToko::insertOrIgnore([
            // --- Toko T001 (Sarinah) ---
            [
                'id_toko' => 'T001',
                'id_produk' => 'PROD001', // Teh Melati Klasik
                'stok' => 120,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_toko' => 'T001',
                'id_produk' => 'PROD002', // Teh Jahe Madu
                'stok' => 85,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_toko' => 'T001',
                'id_produk' => 'PROD004', // English Breakfast Tea
                'stok' => 50,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // --- Toko T002 (Blok M) ---
            [
                'id_toko' => 'T002',
                'id_produk' => 'PROD001', // Teh Melati Klasik juga ada di sini
                'stok' => 75,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_toko' => 'T002',
                'id_produk' => 'PROD003', // Teh Leci Segar
                'stok' => 110,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_toko' => 'T002',
                'id_produk' => 'PROD005', // Teh Rosella Murni
                'stok' => 0, // Contoh stok habis
                'status' => 'habis',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // --- Toko T003 (Taman Anggrek) ---
            [
                'id_toko' => 'T003',
                'id_produk' => 'PROD002', // Teh Jahe Madu
                'stok' => 90,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_toko' => 'T003',
                'id_produk' => 'PROD003', // Teh Leci Segar
                'stok' => 60,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_toko' => 'T003',
                'id_produk' => 'PROD004', // English Breakfast Tea
                'stok' => 45,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Toko T004 (Bassura) sengaja dikosongkan untuk pengujian
        ]);
    }
}