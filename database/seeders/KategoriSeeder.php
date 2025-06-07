<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Kategori::create([
            'id_kategori' => 'K001',
            'nama_kategori' => 'Tea Series',
            'gambar' => 'kategori/Cat-TeaSeries.png',
            'deskripsi' => 'Kategori untuk produk elektronik seperti smartphone, laptop, dan aksesori lainnya.'
        ]);

        \App\Models\Kategori::create([
            'id_kategori' => 'K002',
            'nama_kategori' => 'Milky Series',
            'gambar' => 'kategori/Cat-MilkySeries.png',
            'deskripsi' => 'Kategori untuk produk pakaian pria, wanita, dan anak-anak.'
        ]);

        \App\Models\Kategori::create([
            'id_kategori' => 'K003',
            'nama_kategori' => 'Squash Series',
            'gambar' => 'kategori/Cat-SquashSeries.png',
            'deskripsi' => 'Kategori untuk peralatan rumah tangga seperti perabotan, alat dapur, dan dekorasi.'
        ]);
        \App\Models\Kategori::create([
            'id_kategori' => 'K004',
            'nama_kategori' => 'Thai Tea Series',
            'gambar' => 'kategori/Cat-ThaiTeaSeries.png',
            'deskripsi' => 'Kategori untuk produk olahraga seperti pakaian olahraga, sepatu, dan peralatan fitness.'
        ]);
        \App\Models\Kategori::create([
            'id_kategori' => 'K005',
            'nama_kategori' => 'Yakult Series',
            'gambar' => 'kategori/Cat-YakultSeries.png',
            'deskripsi' => 'Kategori untuk produk kecantikan seperti kosmetik, perawatan kulit, dan parfum.'
        ]);
    }
}
