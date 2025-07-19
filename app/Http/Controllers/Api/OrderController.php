<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Exceptions\PromoUnavailableException;
use App\Exceptions\InsufficientStockException;
use Illuminate\Validation\ValidationException;
use App\Models\Keranjang;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }



    /**
     * Membuat pesanan baru berdasarkan keranjang belanja pengguna.
     */
    public function placeOrder(Request $request)
    {
        Log::info("jsdhsjdhs");
        // 1. Validasi input disesuaikan dengan kebutuhan alur "Store-First"
        $validatedData = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|string|exists:tb_produk,id_produk',
            'items.*.jumlah' => 'required|integer|min:1',

            'selected_promo_id' => 'nullable|string|exists:tb_promo,id_promo',
            'selected_toko_id' => 'required|string|exists:tb_toko,id_toko',
            'metode_pengiriman' => 'required|in:delivery,pickup', // Validasi metode pengiriman

            // Kolom opsional
            'id_alamat' => 'nullable|string|exists:tb_alamat,id_alamat',
            'catatan_pembeli' => 'nullable|string|max:500',
            'metode_pembayaran_dipilih' => 'nullable|string' // e.g., 'QRIS', 'cod'
        ]);

        try {
            $user = Auth::user();

            // 2. Memanggil OrderService dengan parameter yang sudah disesuaikan
            $transaksi = $this->orderService->createOrder(
                $user,
                $validatedData['items'],
                $validatedData['selected_promo_id'] ?? null,
                $validatedData['selected_toko_id'],
                [
                    // Mengelompokkan metadata pesanan
                    'metode_pengiriman' => $validatedData['metode_pengiriman'],
                    'id_alamat' => $validatedData['id_alamat'] ?? null,
                    'catatan_pembeli' => $validatedData['catatan_pembeli'] ?? null,
                ]
            );

            $midtrans = new PaymentController(); 
            $token = $midtrans->createSnapToken($request, $transaksi);

            $transaksi->snap_token = $token;
            $transaksi->save();


            // 3. Menyiapkan data respons yang bersih 
            $responseData = [
                'id_transaksi' => $transaksi->id_transaksi,
                'harga_akhir' => $transaksi->harga_akhir,
                'status' => $transaksi->status, // Menggunakan 'status' sesuai migrasi Anda
                'pembayaran' => [
                    'id_pembayaran' => $transaksi->pembayaran->id_pembayaran,
                    'status' => $transaksi->pembayaran->status, // Menggunakan 'status'
                    'jumlah_dibayar' => $transaksi->pembayaran->jumlah_dibayar,
                    'bukti_bayar' => $transaksi->pembayaran->bukti_bayar,
                ]
            ];

            return response()->json([
                'message' => 'Pesanan berhasil dibuat, menunggu pembayaran.',
                'data' => $responseData
            ], 201); // 201 Created

        } catch (PromoUnavailableException | InsufficientStockException | ValidationException $e) {
            // Menangani error yang sudah diperkirakan (validasi, stok, promo)
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            // Menangani semua error tak terduga lainnya'

            Log::error('Order creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Gagal membuat pesanan, terjadi kesalahan pada server.'], 500);
        }
    }


    public function getOrders(Request $request)
    {
        $user = auth()->user();

        $orders = $this->orderService->getOrdersByUser($user);

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Tidak ada pesanan ditemukan.'], 404);
        }

        return response()->json([
            'message' => 'Daftar pesanan berhasil diambil.',
            'data' => $orders
        ], 200); // 200 OK
    }
}
