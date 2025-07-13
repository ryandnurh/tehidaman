<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProdukToko;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProdukTokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel terlebih dahulu
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tb_produk_toko')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();
        $produkTokoData = [];

        // Generate stok unik untuk setiap toko
        $generateStocks = function($count) {
            $stocks = [];
            for ($i = 0; $i < $count; $i++) {
                $stocks[] = rand(50, 200);
            }
            return $stocks;
        };

        // Untuk setiap toko
        foreach (['T001', 'T002', 'T003'] as $tokoId) {
            $stocks = $generateStocks(34); // 34 produk
            
            // Untuk setiap produk (PROD001 sampai PROD034)
            for ($i = 1; $i <= 34; $i++) {
                $produkId = 'PROD' . str_pad($i, 3, '0', STR_PAD_LEFT);
                
                $produkTokoData[] = [
                    'id_toko' => $tokoId,
                    'id_produk' => $produkId,
                    'stok' => $stocks[$i-1],
                    'status' => 'tersedia',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert data dengan chunk
        foreach (array_chunk($produkTokoData, 50) as $chunk) {
            DB::table('tb_produk_toko')->insert($chunk);
        }
    }
}