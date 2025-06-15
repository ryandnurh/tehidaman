<?php

namespace App\Listeners;

use App\Events\OrderPaymentFailed;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RollbackPromoQuotaListener
{
    /**
     * Menangani event saat pembayaran pesanan gagal atau dibatalkan.
     * Logika ini akan mengembalikan kuota promo yang sebelumnya digunakan.
     *
     * @param \App\Events\OrderPaymentFailed $event
     * @return void
     */
    public function handle(OrderPaymentFailed $event): void
    {
        $transaksi = $event->transaksi;

        // Hanya jalankan jika transaksi ini memang menggunakan promo
        if ($transaksi->id_promo_terpakai) {
            Log::info("RollbackPromoQuotaListener dipicu untuk transaksi: {$transaksi->id_transaksi} dengan promo: {$transaksi->id_promo_terpakai}");

            try {
                DB::transaction(function () use ($transaksi) {
                    // Kunci baris promo untuk mencegah race condition
                    $promo = Promo::lockForUpdate()->find($transaksi->id_promo_terpakai);

                    // Hanya proses jika promo ditemukan dan memiliki kuota yang terbatas
                    if ($promo && $promo->kuota_promo !== null) {
                        
                        // PERBAIKAN: Gunakan 'jumlah_penggunaan' sesuai model Promo Anda
                        if ($promo->jumlah_penggunaan > 0) {
                            $promo->decrement('jumlah_penggunaan');
                        } else {
                            // Ini untuk mencegah nilai menjadi negatif jika ada anomali
                            $promo->jumlah_penggunaan = 0;
                        }

                        // PERBAIKAN: Gunakan 'status' sesuai model Promo Anda
                        // Jika status promo sebelumnya 'habis' dan kuota jadi tersedia lagi,
                        // kembalikan statusnya ke 'aktif' (jika periode promo masih valid).
                        if (
                            $promo->status === 'habis' &&
                            $promo->jumlah_penggunaan < $promo->kuota_promo &&
                            Carbon::now()->between($promo->tanggal_mulai, $promo->tanggal_berakhir)
                        ) {
                            $promo->status = 'aktif';
                        }
                        
                        $promo->save();
                        Log::info("Kuota untuk promo {$promo->id_promo} berhasil dikembalikan. Jumlah penggunaan saat ini: {$promo->jumlah_penggunaan}");
                    }
                });
            } catch (\Exception $e) {
                Log::error("Gagal rollback kuota promo untuk transaksi {$transaksi->id_transaksi}: " . $e->getMessage());
            }
        }
    }
}
