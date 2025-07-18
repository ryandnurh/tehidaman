<?php

namespace App\Services;

use App\Models\User;
use App\Models\Promo;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\ProdukToko;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Exceptions\PromoUnavailableException;
use App\Exceptions\InsufficientStockException;
use Illuminate\Support\Str;

class OrderService
{
    protected $promoService;

    public function __construct(PromoService $promoService)
    {
        $this->promoService = $promoService;
    }

    public function createOrder(User $user, array $rawCartItems, ?string $selectedPromoId, string $selectedTokoId, array $orderMeta): Transaksi
    {
        // Membungkus semua operasi dalam satu transaksi database yang aman.
        return DB::transaction(function () use ($user, $rawCartItems, $selectedPromoId, $selectedTokoId, $orderMeta) {

            // --- TAHAP 1: VALIDASI DATA & HITUNG SUBTOTAL ---
            $subtotalProduk = 0;
            $processedCartItems = [];
            $productIds = array_column($rawCartItems, 'id_produk');
            $productsFromDb = Produk::whereIn('id_produk', $productIds)->get()->keyBy('id_produk');
            $stockData = ProdukToko::where('id_toko', $selectedTokoId)
                                     ->whereIn('id_produk', $productIds)
                                     ->get()->keyBy('id_produk');

            foreach ($rawCartItems as $item) {
                $produk = $productsFromDb->get($item['id_produk']);
                $stokDiToko = $stockData->get($item['id_produk']);

                if (!$produk || !$stokDiToko || $stokDiToko->stok < $item['jumlah'] || $stokDiToko->status !== 'tersedia') {
                    $namaProdukGagal = $produk ? $produk->nama_produk : "ID: {$item['id_produk']}";
                    throw new InsufficientStockException("Stok untuk produk '{$namaProdukGagal}' tidak tersedia atau tidak mencukupi di toko yang dipilih.");
                }

                $subtotalProduk += $produk->harga * $item['jumlah'];
                $processedCartItems[] = [
                    'id_produk' => $produk->id_produk,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $produk->harga,
                    'subtotal_item' => $produk->harga * $item['jumlah']
                ];
            }

            // --- TAHAP 2: VALIDASI & KALKULASI PROMO ---
            $finalDiscountAmount = 0;
            $finalPromoIdToStore = null;
            $promoToConsume = null;

            if ($selectedPromoId) {
                $promoToConsume = Promo::lockForUpdate()->find($selectedPromoId);

                if (!$this->promoService->isPromoApplicable($promoToConsume, $user, $processedCartItems, $subtotalProduk, $productsFromDb)) {
                     throw new PromoUnavailableException("Promo tidak berlaku untuk keranjang atau pengguna ini.");
                }

                // Kalkulasi diskon (asumsi tidak ada ongkir)
                $finalDiscountAmount = $this->promoService->calculateDiscountAmount($promoToConsume, $processedCartItems, $subtotalProduk, $productsFromDb, 0);
                if ($finalDiscountAmount > 0) {
                    $finalPromoIdToStore = $promoToConsume->id_promo;
                } else {
                    $promoToConsume = null;
                }
            }

            // --- TAHAP 3: KALKULASI HARGA FINAL & BUAT DATA ---
            $hargaAkhir = max(0, $subtotalProduk - $finalDiscountAmount);

            // 3.1 Buat Transaksi 
            $transaksi = Transaksi::create([
                'id_transaksi' => 'TRX' . strtoupper(uniqid()),
                'id_user' => $user->id_user,
                'id_toko' => $selectedTokoId,
                'metode_pengiriman' => $orderMeta['metode_pengiriman'],
                'catatan_pembeli' => $orderMeta['catatan_pembeli'] ?? null,
                'id_alamat' => $orderMeta['id_alamat'] ?? null,
                'total_harga' => $subtotalProduk, // Nama kolom dari migrasi Anda
                'id_promo_terpakai' => $finalPromoIdToStore,
                'diskon' => $finalDiscountAmount,
                'harga_akhir' => $hargaAkhir,
                'status' => 'menunggu pembayaran', // Nilai ENUM dari migrasi Anda
            ]);

            // 3.2 Buat Detail Transaksi 
            foreach ($processedCartItems as $item) {
                $transaksi->detail()->create([ // Pastikan relasi di model Transaksi bernama 'detail()'
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['jumlah'],
                    'harga_total' => $item['subtotal_item'], // Nama kolom dari migrasi Anda
                ]);
            }

            // 3.3 Buat Entri Pembayaran 
            $pembayaran = $transaksi->pembayaran()->create([ // Pastikan relasi di model Transaksi bernama 'pembayaran()'
                'id_pembayaran' => 'PAY' . strtoupper(uniqid()),
                'metode pembayaran' => $orderMeta['metode_pembayaran_dipilih'] ?? 'QRIS', // Default ke QRIS jika tidak ada
                'bukti_bayar' => 'Belum ada bukti bayar', // Placeholder awal
                'status' => 'menunggu pembayaran',
            ]);
            $transaksi->setRelation('pembayaran', $pembayaran);

            // --- TAHAP 4: UPDATE STATE (KONSUMSI KUOTA & STOK) ---
            if ($promoToConsume) {
                if ($promoToConsume->kuota_promo !== null) {
                    $promoToConsume->increment('jumlah_penggunaan'); // Kolom dari model Promo Anda
                    if ($promoToConsume->jumlah_penggunaan >= $promoToConsume->kuota_promo) {
                        $promoToConsume->status = 'habis'; // Kolom dari model Promo Anda
                        $promoToConsume->save();
                    }
                }
            }
            
            foreach ($processedCartItems as $item) {
                ProdukToko::where('id_toko', $selectedTokoId)
                          ->where('id_produk', $item['id_produk'])
                          ->decrement('stok', $item['jumlah']);
            }
            
            return $transaksi;
        });
    }

    public function getOrdersByUser(User $user)
    {
        $transaksi = Transaksi::select('id_transaksi', 'id_user', 'id_toko', 'metode_pengiriman',
        'harga_akhir', 'status', 'created_at')
            ->where('id_user', $user->id_user)
            ->get();

        return $transaksi;
    }
}