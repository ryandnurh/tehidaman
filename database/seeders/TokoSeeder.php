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
            'nama_toko' => 'Toko Contoh',
            'alamat_toko' => 'Jl. Contoh No. 1, Kota Contoh',
            'no_hp_toko' => '081234567890',
            'username_admin' => 'admin1',
            'password_admin' => bcrypt('12345678'),
            'latitude' => -6.189950,
            'longitude' => 106.822630
        ]);

        Toko::create([
            'id_toko' => 'T002',
            'nama_toko' => 'Toko Contoh 2',
            'alamat_toko' => 'Jl. Contoh No. 2, Kota Contoh',
            'no_hp_toko' => '081234567891',
            'username_admin' => 'admin2',
            'password_admin' => bcrypt('12345678'),
            'latitude' => -6.244380,
            'longitude' => 106.799340
        ]);

        Toko::create([
            'id_toko' => 'T003',
            'nama_toko' => 'Toko Contoh 3',
            'alamat_toko' => 'Jl. Contoh No. 3, Kota Contoh',
            'no_hp_toko' => '081234567892',
            'username_admin' => 'admin3',
            'password_admin' => bcrypt('12345678'),
            'latitude' => -6.178330,
            'longitude' => 106.792490
        ]);

        Toko::create([
            'id_toko' => 'T004',
            'nama_toko' => 'Toko Contoh 4',
            'alamat_toko' => 'Jl. Contoh No. 4, Kota Contoh',
            'no_hp_toko' => '081234567893',
            'username_admin' => 'admin4',
            'password_admin' => bcrypt('12345678'),
            'latitude' => -6.225940,
            'longitude' => 106.880480
        ]);
    }
}
