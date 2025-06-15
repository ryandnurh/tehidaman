<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Events\OrderPaymentFailedOrCancelled;
use App\Events\OrderPaid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\OrderPaymentFailed;


class PaymentStatusController extends Controller
{
    /**
     * Memperbarui status pembayaran dan status pesanan.
     * Memicu event yang sesuai (rollback atau proses lanjut).
     */
    public function updatePaymentStatus(Request $request, string $id_transaksi)
    {
        // 1. Validasi input disesuaikan dengan ENUM di tb_pembayaran
        $validatedData = $request->validate([
            'new_payment_status' => 'required|string|in:terbayar,gagal',
        ]);

        $newPaymentStatus = $validatedData['new_payment_status'];
        $message = '';

        try {
            DB::transaction(function () use ($id_transaksi, $newPaymentStatus, &$message) {
                
                // 2. Ambil transaksi, pastikan status awalnya adalah 'menunggu pembayaran'
                $transaksi = Transaksi::where('id_transaksi', $id_transaksi)
                    ->where('status', 'menunggu pembayaran') 
                    ->firstOrFail();
                    
                $pembayaran = Pembayaran::where('id_transaksi', $transaksi->id_transaksi)->firstOrFail();

                // Hanya proses jika statusnya memang berubah
                if ($pembayaran->status === $newPaymentStatus) {
                    $message = "Status pembayaran sudah {$newPaymentStatus}. Tidak ada perubahan.";
                    return;
                }

                $pembayaran->status = $newPaymentStatus;
                
                // 3. Logika utama berdasarkan status baru
                if ($newPaymentStatus === 'terbayar') {
                    
                    // Ubah status transaksi ke langkah berikutnya: 'sedang dibuat'
                    $transaksi->status = 'sedang dibuat';
                    
                    $message = "Status pembayaran untuk transaksi {$id_transaksi} berhasil diubah menjadi 'terbayar'. Pesanan akan diproses.";
                    
                    // Picu event untuk proses selanjutnya (notifikasi, dll.)
                    event(new OrderPaid($transaksi));

                } else { // Kasus 'gagal'
                    // Ubah status transaksi menjadi 'gagal' 
                    $transaksi->status = 'gagal';
                    
                    $message = "Status pembayaran untuk transaksi {$id_transaksi} diubah menjadi 'gagal'. Proses rollback dipicu.";
                    
                    // Picu event untuk rollback kuota promo dan stok produk
                    event(new OrderPaymentFailed($transaksi));
                }
                
                $pembayaran->save();
                $transaksi->save();

            }); // Akhir DB Transaction

            return response()->json(['message' => $message]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaksi tidak ditemukan atau statusnya bukan "menunggu pembayaran".'], 404);
        } catch (\Exception $e) {
            Log::error("Gagal update status pembayaran untuk {$id_transaksi}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}