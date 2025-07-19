<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Events\OrderPaid;
use App\Events\OrderPaymentFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans; // Import Midtrans

class MidtransNotificationService
{
    /**
     * Memproses notifikasi yang sudah dalam bentuk objek Midtrans.
     *
     * @param \Midtrans\Notification $notification
     * @return void
     */
    public function process(Midtrans\Notification $notification): void
    {
        // 1. Verifikasi Signature Key (Keamanan)
        $signature = hash('sha512', $notification->order_id . $notification->status_code . $notification->gross_amount . config('midtrans.server_key'));

        Log::info("midtrans notif line 25 jalan");
        if ($notification->signature_key !== $signature) {
            Log::warning("Midtrans Webhook: Invalid signature key untuk order ID {$notification->order_id}");
            return; // Hentikan proses jika signature tidak valid
        }

        // 2. Ambil data dari objek notifikasi
        $id_transaksi = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status;
        
        // 3. Cari transaksi di database Anda
        $transaksi = Transaksi::find($id_transaksi);
        if (!$transaksi) {
            Log::error("Midtrans Webhook: Transaksi dengan ID {$id_transaksi} tidak ditemukan.");
            return;
        }

        $pembayaran = $transaksi->pembayaran;
        if (!$pembayaran || $pembayaran->status === 'terbayar' || $pembayaran->status === 'gagal') {
            Log::info("Midtrans Webhook: Transaksi {$id_transaksi} sudah pernah diproses. Status saat ini: {$pembayaran->status}");
            return; // Hindari pemrosesan ulang
        }

        // 4. Update status berdasarkan notifikasi
        DB::transaction(function () use ($transactionStatus, $fraudStatus, $pembayaran, $transaksi) {
            
            // Logika untuk pembayaran sukses
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($fraudStatus == 'accept') {
                    $pembayaran->status = 'terbayar';
                    $transaksi->status = 'sedang dibuat';
                    $pembayaran->save();
                    $transaksi->save();
                    event(new OrderPaid($transaksi));
                    Log::info("Midtrans Webhook: Pembayaran untuk transaksi {$transaksi->id_transaksi} berhasil.");
                }
            } 
            // Logika untuk pembayaran gagal/dibatalkan/kadaluarsa
            elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $pembayaran->status = 'gagal';
                $transaksi->status = 'gagal';
                $pembayaran->save();
                $transaksi->save();
                event(new OrderPaymentFailed($transaksi));
                Log::info("Midtrans Webhook: Pembayaran untuk transaksi {$transaksi->id_transaksi} gagal atau dibatalkan.");
            }
        });
    }
}
