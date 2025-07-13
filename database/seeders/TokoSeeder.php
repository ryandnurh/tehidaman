<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Toko;

class TokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Toko::create([
            'id_toko' => 'T001',
            'nama_toko' => 'Teh Idaman Concat',
            'alamat_toko' => 'Jl. Tantular No.11, Kaliwaru, Condongcatur, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55281',
            'no_hp_toko' => '081234567890',
            'username_admin' => 'adminconcat',
            'password_admin' => bcrypt('12345678'),
            'latitude' => -6.189950,
            'longitude' => 106.822630
        ]);

        Toko::create([
            'id_toko' => 'T002',
            'nama_toko' => 'Teh Idaman Gejayan',
            'alamat_toko' => 'Gg. Bayu Jl. Affandi No.15, Gejayan, Caturtunggal, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55281',
            'no_hp_toko' => '081234567891',
            'username_admin' => 'admingejayan',
            'password_admin' => bcrypt('12345678'),
            'latitude' => -6.244380,
            'longitude' => 106.799340
        ]);

        Toko::create([
            'id_toko' => 'T003',
            'nama_toko' => 'Teh Idaman Wonosari',
            'alamat_toko' => 'Jl. Baron No.KM 1, Seneng, Siraman, Kec. Wonosari, Kabupaten Gunungkidul, Daerah Istimewa Yogyakarta 55851',
            'no_hp_toko' => '081234567892',
            'username_admin' => 'adminwonosari',
            'password_admin' => bcrypt('12345678'),
            'latitude' => -6.178330,
            'longitude' => 106.792490
        ]);
    }
}
