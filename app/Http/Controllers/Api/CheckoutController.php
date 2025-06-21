<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\ProdukToko;
use App\Models\Alamat;
use App\Services\PromoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    protected $promoService;

    public function __construct(PromoService $promoService)
    {
        $this->promoService = $promoService;
    }

    /**
     * Menyiapkan atau memperbarui data untuk halaman checkout.
     * Endpoint ini berfungsi untuk:
     * 1. Memuat data awal saat halaman checkout dibuka.
     * 2. Menerapkan promo yang dipilih pengguna.
     * 3. Me-refresh halaman dengan promo yang sudah diterapkan.
     */
    public function prepareCheckout(Request $request)
    {
        // 1. Validasi input. Semua data state dikirim dari frontend.
        $validatedData = $request->validate([
            'selected_toko_id' => 'required|string|exists:tb_toko,id_toko',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|string|exists:tb_produk,id_produk',
            'items.*.jumlah' => 'required|integer|min:1',
            'selected_promo_id' => 'nullable|string|exists:tb_promo,id_promo',
        ]);

        $user = Auth::user();
        $tokoId = $validatedData['selected_toko_id'];
        $rawCartItems = $validatedData['items'];
        $selectedPromoId = $validatedData['selected_promo_id'] ?? null;

        $alamatUser = Alamat::where('id_user', $user->id_user)->where('status', 'utama')->first();

        if ($alamatUser === null) {
            return response()->json([
                'message' => 'Pengguna tidak memiliki alamat utama yang ditetapkan.',
                'error_type' => 'NO_PRIMARY_ADDRESS',
                'data' => []
            ], 422);
        }

        // --- TAHAP VALIDASI & KALKULASI ---
        
        $subtotalProduk = 0;
        $validatedItemsForResponse = [];
        $itemsForPromoService = [];

        // 2. Validasi ulang setiap item dari request terhadap database
        foreach ($rawCartItems as $item) {
            $produk = Produk::find($item['id_produk']);
            $stokTerkini = ProdukToko::where('id_toko', $tokoId)
                                      ->where('id_produk', $item['id_produk'])
                                      ->first();

            if (!$produk || !$stokTerkini || $stokTerkini->stok < $item['jumlah'] || $stokTerkini->status !== 'tersedia') {
                return response()->json([
                    'message' => "Stok untuk produk '{$produk->nama_produk}' tidak mencukupi atau tidak tersedia.",
                    'error_type' => 'INSUFFICIENT_STOCK',
                    'item_bermasalah' => $item['id_produk'],
                ], 422);
            }

            $subtotalItem = $produk->harga * $item['jumlah'];
            $subtotalProduk += $subtotalItem;

            $validatedItemsForResponse[] = [
                'id_produk' => $produk->id_produk,
                'nama_produk' => $produk->nama_produk,
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $produk->harga,
                'subtotal' => $subtotalItem
            ];

            $itemsForPromoService[] = ['id_produk' => $produk->id_produk, 'jumlah' => $item['jumlah'], 'harga_satuan' => $produk->harga];
        }

        // --- TAHAP PROMO ---

        $ringkasanHarga = ['subtotal' => $subtotalProduk, 'diskon' => 0, 'total_akhir' => $subtotalProduk];
        $promoTerpilih = null;
        
        // 3. Jika frontend mengirimkan ID promo yang dipilih, validasi dan hitung diskonnya
        if ($selectedPromoId) {
            $promoResult = $this->promoService->validateAndPreviewChosenPromo($user, $selectedPromoId, 'id', $itemsForPromoService, 0);

            if ($promoResult['success']) {
                $ringkasanHarga = $promoResult['data']['cart_summary'];
                $promoTerpilih = $promoResult['data']['applied_promo_details'];
            }
            // Jika tidak sukses, kita abaikan saja promo yang tidak valid itu.
            // Frontend akan melihat 'promo_terpilih' bernilai null dan bisa memberi notifikasi.
        }

        // 4. Tetap ambil daftar rekomendasi promo lain yang bisa dipilih
        $rekomendasiPromo = $this->promoService->getRecommendedPromos($user, $itemsForPromoService, 0);

        // --- TAHAP AKHIR: KIRIM RESPON LENGKAP ---

        return response()->json([
            'message' => 'Data checkout berhasil disiapkan.',
            'data' => [
                'items' => $validatedItemsForResponse,
                'alamat_pengguna' => $alamatUser,
                'toko_terpilih' => ['id_toko' => $tokoId],
                'ringkasan_harga' => $ringkasanHarga,
                'promo_terpilih' => $promoTerpilih, // Bisa null jika tidak ada/tidak valid
                'rekomendasi_promo' => $rekomendasiPromo
            ]
        ]);
    }
}
