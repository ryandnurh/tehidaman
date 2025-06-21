<?php
namespace App\Services;

use App\Models\Transaksi;
use App\Events\OrderPaid;
use App\Events\OrderPaymentFailed;
use Illuminate\Support\Facades\DB;
use Midtrans;

class MidtransNotificationService
{
    public function process(array $notificationPayload)
    {
        // Set server key Anda untuk verifikasi
        Midtrans\Config::$serverKey = config('midtrans.server_key');

        // Buat objek notifikasi dari payload
        $notification = new Midtrans\Notification($notificationPayload);

        // Verifikasi signature key (keamanan)
        $orderId = $notification->order_id;
        $statusCode = $notification->status_code;
        $grossAmount = $notification->gross_amount;
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));

        if ($notification->signature_key !== $signature) {
            // Jika signature tidak valid, abaikan notifikasi ini.
            return;
        }

        // Temukan transaksi dan update statusnya
        $transaksi = Transaksi::find($orderId);
        if (!$transaksi) return;

        $transactionStatus = $notification->transaction_status;

        // Gunakan logic yang sudah ada di PaymentStatusService atau langsung di sini
        DB::transaction(function () use ($transactionStatus, $transaksi) {
            $pembayaran = $transaksi->pembayaran;
            if ($pembayaran->status === 'terbayar' || $pembayaran->status === 'gagal') {
                return; // Sudah diproses, jangan proses lagi
            }

            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                $pembayaran->status = 'terbayar';
                $transaksi->status = 'sedang dibuat';
                event(new OrderPaid($transaksi));
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $pembayaran->status = 'gagal';
                $transaksi->status = 'gagal';
                event(new OrderPaymentFailed($transaksi));
            }

            $pembayaran->save();
            $transaksi->save();
        });
    }
}