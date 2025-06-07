<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        Produk::create([
            'id_produk' => 'P001',
            'id_kategori' => 'K001',
            'gambar_produk' => 'path/to/image.jpg',
            'nama_produk' => 'Produk Contoh',
            'deskripsi' => 'Deskripsi produk contoh',
            'harga' => 10000,
            'jumlah_terjual' => 0
        ]);

        Produk::create([
            'id_produk' => 'P002',
            'id_kategori' => 'K002',
            'gambar_produk' => 'path/to/image2.jpg',
            'nama_produk' => 'Produk Contoh 2',
            'deskripsi' => 'Deskripsi produk contoh 2',
            'harga' => 20000,
            'jumlah_terjual' => 0
        ]);

        Produk::create([
            'id_produk' => 'P003',
            'id_kategori' => 'K003',
            'gambar_produk' => 'path/to/image3.jpg',
            'nama_produk' => 'Produk Contoh 3',
            'deskripsi' => 'Deskripsi produk contoh 3',
            'harga' => 30000,
            'jumlah_terjual' => 0
        ]);

        Produk::create([
            'id_produk' => 'P004',
            'id_kategori' => 'K004',
            'gambar_produk' => 'path/to/image4.jpg',
            'nama_produk' => 'Produk Contoh 4',
            'deskripsi' => 'Deskripsi produk contoh 4',
            'harga' => 40000,
            'jumlah_terjual' => 0
        ]);
    }
}
