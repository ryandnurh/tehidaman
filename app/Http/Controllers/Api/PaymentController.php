<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Midtrans;

class PaymentController extends Controller
{
    public function createSnapToken(Request $request, Transaksi $transaksi)
    {
        // Pastikan user yang request adalah pemilik transaksi
        if ($request->user()->id_user !== $transaksi->id_user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($transaksi->status !== 'menunggu pembayaran') {
            return response()->json(['message' => 'Transaksi ini sudah tidak bisa dibayar.'], 422);
        }

        // Siapkan parameter untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $transaksi->id_transaksi, 
                'gross_amount' => (int) $transaksi->harga_akhir,
            ],
            'customer_details' => [
                'first_name' => $transaksi->user->nama,
                'email' => $transaksi->user->email,
                'phone' => $transaksi->user->no_hp,
            ],
'callbacks' => [
'finish' => 'tehidaman://payment/success'],
        ];

        try {
            // Dapatkan Snap Token dari Midtrans
            $snapToken = Midtrans\Snap::getSnapToken($params);

            return $snapToken;

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat token pembayaran: ' . $e->getMessage()], 500);
        }
    }
}