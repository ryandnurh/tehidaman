<?php

namespace App\Listeners;

use App\Events\OrderPaymentFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ProdukToko;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RollbackStockListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Menangani event saat pembayaran pesanan gagal atau dibatalkan.
     *
     * @param \App\Events\OrderPaymentFailed $event
     * @return void
     */
    public function handle(OrderPaymentFailed $event): void
    {
        $transaksi = $event->transaksi;
        Log::info("RollbackStockListener dipicu untuk transaksi: " . $transaksi->id_transaksi);

        // Pastikan relasi 'detail' ada di model Transaksi
        $detailItems = $transaksi->detail;

        if ($detailItems->isEmpty()) {
            Log::warning("Tidak ada detail item ditemukan untuk rollback stok pada transaksi: " . $transaksi->id_transaksi);
            return;
        }

        try {
            DB::transaction(function () use ($detailItems, $transaksi) {
                foreach ($detailItems as $item) {
                    
                    // --- PERBAIKAN UTAMA DI SINI ---
                    
                    // Kita akan melakukan update langsung ke database daripada memuat dan menyimpan model.
                    // Gunakan DB::raw() untuk melakukan penambahan stok secara atomik.
                    $affectedRows = ProdukToko::where('id_toko', $transaksi->id_toko)
                        ->where('id_produk', $item->id_produk)
                        ->update([
                            'stok' => DB::raw("stok + {$item->jumlah}"),
                            // Kita juga bisa mengupdate status menjadi 'tersedia' di sini
                            // jika kita yakin status sebelumnya adalah 'habis'
                            // Untuk keamanan, kita bisa set selalu ke 'tersedia' jika stok menjadi > 0
                            'status' => 'tersedia'
                        ]);

                    if ($affectedRows > 0) {
                        Log::info("Stok untuk produk {$item->id_produk} di toko {$transaksi->id_toko} berhasil dikembalikan sebanyak {$item->jumlah}.");
                    } else {
                        Log::error("Gagal rollback stok: Entri ProdukToko tidak ditemukan untuk produk {$item->id_produk} di toko {$transaksi->id_toko}.");
                    }
                }
            });
        } catch (\Exception $e) {
            Log::critical("Exception saat rollback stok untuk transaksi {$transaksi->id_transaksi}: " . $e->getMessage());
        }
    }
}
