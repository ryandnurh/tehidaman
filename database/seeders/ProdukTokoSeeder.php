<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProdukToko;

class ProdukTokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProdukToko::create([
            'id_produk' => 'P001',
            'id_toko' => 'T001',
            'stok' => 100,
            'status' => 'Tersedia'
        ]);

        ProdukToko::create([
            'id_produk' => 'P001',
            'id_toko' => 'T002',
            'stok' => 50,
            'status' => 'Tersedia'
        ]);
        ProdukToko::create([
            'id_produk' => 'P002',
            'id_toko' => 'T001',
            'stok' => 200,
            'status' => 'Tersedia'
        ]);
        ProdukToko::create([
            'id_produk' => 'P002',
            'id_toko' => 'T003',
            'stok' => 150,
            'status' => 'Tersedia'
        ]);
        ProdukToko::create([
            'id_produk' => 'P003',
            'id_toko' => 'T001',
            'stok' => 300,
            'status' => 'Tersedia'
        ]);
    }
}
