<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use Carbon\Carbon;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Menggunakan insert agar lebih efisien untuk banyak data, 
        // pastikan tidak ada data duplikat jika menjalankan ulang tanpa migrate:fresh
        Produk::insert([
            [
                'id_produk' => 'PROD001',
                'id_kategori' => 'KAT004', // Asumsi KAT004 adalah Teh Melati
                'nama_produk' => 'Teh Melati Klasik',
                'deskripsi' => 'Perpaduan teh hijau pilihan dengan bunga melati asli yang memberikan aroma menenangkan dan rasa yang lembut.',
                'gambar_produk' => 'produk/teh_melati.jpg', // Path contoh
                'harga' => 18000.00,
                'jumlah_terjual' => 152, // Contoh jumlah terjual
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_produk' => 'PROD002',
                'id_kategori' => 'KAT002', // Asumsi KAT002 adalah Teh Herbal
                'nama_produk' => 'Teh Jahe Madu',
                'deskripsi' => 'Kehangatan jahe merah bertemu dengan manisnya madu alami, cocok untuk menjaga daya tahan tubuh.',
                'gambar_produk' => 'produk/teh_jahe.jpg',
                'harga' => 22000.00,
                'jumlah_terjual' => 95,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_produk' => 'PROD003',
                'id_kategori' => 'KAT003', // Asumsi KAT003 adalah Teh Buah
                'nama_produk' => 'Teh Leci Segar',
                'deskripsi' => 'Rasakan kesegaran tropis dari teh dengan ekstrak buah leci asli yang menyegarkan.',
                'gambar_produk' => 'produk/teh_leci.jpg',
                'harga' => 20000.00,
                'jumlah_terjual' => 110,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_produk' => 'PROD004',
                'id_kategori' => 'KAT001', // Asumsi KAT001 adalah Teh Klasik
                'nama_produk' => 'English Breakfast Tea',
                'deskripsi' => 'Teh hitam pekat dengan rasa yang kuat dan kaya, sempurna untuk memulai hari Anda.',
                'gambar_produk' => 'produk/english_breakfast.jpg',
                'harga' => 28000.00,
                'jumlah_terjual' => 78,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_produk' => 'PROD005',
                'id_kategori' => 'KAT002', // Asumsi KAT002 adalah Teh Herbal
                'nama_produk' => 'Teh Rosella Murni',
                'deskripsi' => 'Teh herbal dari bunga rosella murni yang kaya akan antioksidan dan vitamin C.',
                'gambar_produk' => 'produk/teh_rosella.jpg',
                'harga' => 24000.00,
                'jumlah_terjual' => 65,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}