<?php

namespace App\Services;

use App\Models\Promo;
use App\Models\User;
use App\Models\Produk;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Exceptions\PromoUnavailableException;

class PromoService
{
    public function calculateCartSubtotal(array $cartItems): float
    {
        return array_reduce($cartItems, function ($carry, $item) {
            return $carry + (($item['harga_satuan'] ?? 0) * ($item['jumlah'] ?? 0));
        }, 0.0);
    }

    public function getProductsInCartDetails(array $cartItems): Collection
    {
        $productIdsInCart = array_column($cartItems, 'id_produk');
        return Produk::whereIn('id_produk', $productIdsInCart)->with('kategori')->get()->keyBy('id_produk');
    }

    public function isPromoApplicable(Promo $promo, User $user, array $cartItems, float $cartSubtotal, Collection $productsInCartDetails): bool
    {
        // Validasi 1: Cek target pengguna (MENGGUNAKAN NAMA RELASI DAN KOLOM ANDA)
        if ($promo->target_user_baru) {
            // Menggunakan transaksi() dan status='selesai' sesuai permintaan Anda
            $isNewUser = $user->transaksi()->where('status', 'selesai')->doesntExist();
            
            if (!$isNewUser) {
                return false;
            }
        }

        $subtotalForPromoCheck = $cartSubtotal;

        // Validasi 2: Cek target produk/kategori
        if (!$promo->target_semua_produk) {
            $applicableItemsSubtotal = 0;
            $hasApplicableItem = false;

            foreach ($cartItems as $item) {
                $productDetail = $productsInCartDetails->get($item['id_produk']);
                if (!$productDetail) {
                    continue;
                }
                // Cek apakah item ini memenuhi syarat promo
                $isItemEligible = false;
                if ($promo->id_produk_target && trim($promo->id_produk_target) == trim($productDetail->id_produk)) {
                    $isItemEligible = true;
                } elseif ($promo->id_kategori_target && $productDetail->kategori && trim($promo->id_kategori_target) == trim($productDetail->kategori->id_kategori)) {
                    $isItemEligible = true;
                }

                if ($isItemEligible) {
                    $hasApplicableItem = true;
                    $applicableItemsSubtotal += (($item['harga_satuan'] ?? 0) * ($item['jumlah'] ?? 0));
                }
            }

            if (!$hasApplicableItem) {
                return false;
            }

            $subtotalForPromoCheck = $applicableItemsSubtotal;
        }

        // Validasi 3: Cek minimal pembelian
        if ($subtotalForPromoCheck < $promo->minimal_pembelian) {
            return false;
        }

        return true;
    }

    public function calculateDiscountAmount(Promo $promo, array $cartItems, float $cartSubtotal, Collection $productsInCartDetails, ?float $estimatedShippingCost): float
    {
        // (kode asli Anda untuk method ini)
        $subtotalForDiscount = $cartSubtotal;
        
        if (!$promo->target_semua_produk) {
            $subtotalForDiscount = 0;
            foreach ($cartItems as $item) {
                $productDetail = $productsInCartDetails->get($item['id_produk']);
                if (!$productDetail)
                    continue;

                $isItemEligible = false;
                if ($promo->id_produk_target && $promo->id_produk_target === $productDetail->id_produk)
                    $isItemEligible = true;
                elseif ($promo->id_kategori_target && $productDetail->kategori && $promo->id_kategori_target === $productDetail->kategori->id_kategori)
                    $isItemEligible = true;

                if ($isItemEligible)
                    $subtotalForDiscount += (($item['harga_satuan'] ?? 0) * ($item['jumlah'] ?? 0));
            }
            if ($subtotalForDiscount == 0 && $promo->jenis !== 'gratis_ongkir')
                return 0;
        }

        $discount = 0;
        
        switch ($promo->jenis) {
            case 'persentase':
                $discount = ($promo->nilai_diskon / 100) * $subtotalForDiscount;
                if ($promo->maksimal_diskon_nominal !== null && $discount > $promo->maksimal_diskon_nominal) {
                    $discount = $promo->maksimal_diskon_nominal;
                }
                break;
            case 'nominal':
                if (!$promo->target_semua_produk && $promo->id_produk_target) {
                    $eligibleItemQuantity = 0;
                    // Cari item yang cocok di keranjang
                    foreach ($cartItems as $item) {
                        if ($item['id_produk'] === $promo->id_produk_target) {
                            $eligibleItemQuantity = $item['jumlah'];
                            break; // Asumsi promo nominal hanya untuk 1 jenis produk target
                        }
                    }

                    if ($eligibleItemQuantity > 0) {
                        // Kalikan diskon dengan jumlah item yang cocok
                        $discount = $promo->nilai_diskon * $eligibleItemQuantity;
                    } else {
                        // Ini seharusnya tidak terjadi jika validasi isPromoApplicable sudah benar
                        $discount = 0;
                    }

                } else {
                    $discount = $promo->nilai_diskon;
                }
                break;
            case 'gratis_ongkir':
                $discount = $estimatedShippingCost ?? $promo->nilai_diskon ?? 0;
                break;
        }
        if ($promo->jenis !== 'gratis_ongkir' && $discount > $subtotalForDiscount) {
            $discount = $subtotalForDiscount;
        }
        return $discount > 0 ? round($discount, 2) : 0;
    }

    public function generateDiscountUIDescription(Promo $promo, float $discountAmount): string
    {
        if ($promo->deskripsi_singkat_rekomendasi)
            return $promo->deskripsi_singkat_rekomendasi;
        switch ($promo->jenis) {
            case 'persentase':
                return "Diskon {$promo->nilai_diskon}% (Hemat Rp " . number_format($discountAmount, 0, ',', '.') . ")";
            case 'nominal':
                return "Potongan Rp " . number_format($discountAmount, 0, ',', '.');
            case 'gratis_ongkir':
                return "Gratis Ongkir" . ($discountAmount > 0 ? " (Senilai Rp " . number_format($discountAmount, 0, ',', '.') . ")" : "");
            default:
                return $promo->nama_promo;
        }
    }

    public function getRecommendedPromos(User $user, array $cartItems, ?float $estimatedShippingCost = 0): array
    {
        // 1. Ambil semua promo yang secara umum aktif (cek status, tanggal, kuota)
        $availablePromos = Promo::activeForUser()->get();
        $applicablePromos = new Collection();

        // 2. Hitung detail keranjang sekali saja untuk efisiensi
        $cartSubtotal = $this->calculateCartSubtotal($cartItems);
        $productsInCartDetails = $this->getProductsInCartDetails($cartItems);
        

        // 3. Ulangi setiap promo yang aktif
        foreach ($availablePromos as $promo) {
            // Panggil fungsi validasi di bawah ini untuk mengecek apakah promo bisa digunakan
            if ($this->isPromoApplicable($promo, $user, $cartItems, $cartSubtotal, $productsInCartDetails)) {

                // Jika valid, hitung potensi diskonnya
                $discountAmount = $this->calculateDiscountAmount($promo, $cartItems, $cartSubtotal, $productsInCartDetails, $estimatedShippingCost);
                
                // Hanya tambahkan ke daftar jika promo memberi manfaat (diskon > 0 atau promo gratisan)
                if ($discountAmount > 0 || in_array($promo->jenis, ['gratis_ongkir', 'produk_gratis'])) {
                    $applicablePromos->push([
                        'promo_detail' => $promo,
                        'potensi_diskon_rp' => $discountAmount,
                        'deskripsi_diskon_tampil' => $this->generateDiscountUIDescription($promo, $discountAmount)
                    ]);
                }
            }
        }

        // 4. Urutkan promo yang valid berdasarkan potongan diskon terbesar, lalu kembalikan hasilnya
        return $applicablePromos->sortByDesc('potensi_diskon_rp')->values()->all();
    }

    public function validateAndPreviewChosenPromo(User $user, string $promoIdentifier, string $identifierType, array $cartItems, ?float $estimatedShippingCost = 0): array
    {
        $promo = ($identifierType === 'id') ? Promo::find($promoIdentifier) : Promo::where('kode_promo', $promoIdentifier)->first();
        $cartSubtotal = $this->calculateCartSubtotal($cartItems);
        $productsInCartDetails = $this->getProductsInCartDetails($cartItems);

        $cartSummaryWithoutPromo = [
            'subtotal_produk' => $cartSubtotal,
            'diskon_diterapkan_rp' => 0,
            'total_akhir' => $cartSubtotal + ($estimatedShippingCost ?? 0)
        ];

        if (!$promo)
            return ['success' => false, 'message' => 'Promo tidak ditemukan.', 'data' => ['cart_summary' => $cartSummaryWithoutPromo]];

        $now = Carbon::now();
        if (
            $promo->status !== 'aktif' ||
            $now->lt($promo->tanggal_mulai) || $now->gt($promo->tanggal_berakhir) ||
            ($promo->kuota_promo !== null && $promo->jumlah_penggunaan >= $promo->kuota_promo)
        ) {
            return ['success' => false, 'message' => 'Promo tidak lagi berlaku atau kuota habis.', 'data' => ['cart_summary' => $cartSummaryWithoutPromo]];
        }

        if (!$this->isPromoApplicable($promo, $user, $cartItems, $cartSubtotal, $productsInCartDetails)) {
            return ['success' => false, 'message' => 'Syarat promo tidak terpenuhi untuk keranjang Anda.', 'data' => ['cart_summary' => $cartSummaryWithoutPromo]];
        }

        $discountAmount = $this->calculateDiscountAmount($promo, $cartItems, $cartSubtotal, $productsInCartDetails, $estimatedShippingCost);
        if ($discountAmount <= 0 && !in_array($promo->jenis, ['gratis_ongkir', 'produk_gratis'])) {
            return ['success' => false, 'message' => 'Promo ini tidak memberikan potongan untuk keranjang Anda.', 'data' => ['cart_summary' => $cartSummaryWithoutPromo]];
        }

        $appliedPromoDetails = [
            'id_promo' => $promo->id_promo,
            'kode_promo' => $promo->kode_promo,
            'nama_promo' => $promo->nama_promo,
            'jenis' => $promo->jenis,
            'diskon_dihitung_rp' => $discountAmount,
            'deskripsi_diskon_tampil' => $this->generateDiscountUIDescription($promo, $discountAmount)
        ];

        $shippingAfterPromo = ($promo->jenis === 'gratis_ongkir' && $discountAmount > 0) ? (($estimatedShippingCost ?? 0) - $discountAmount) : ($estimatedShippingCost ?? 0);
        $shippingAfterPromo = max(0, $shippingAfterPromo); // Ongkir tidak bisa negatif

        $totalAkhirDenganPromo = ($promo->jenis === 'gratis_ongkir') ? ($cartSubtotal + $shippingAfterPromo) : ($cartSubtotal - $discountAmount + $shippingAfterPromo);


        return [
            'success' => true,
            'message' => 'Promo berhasil divalidasi.',
            'data' => [
                'applied_promo_details' => $appliedPromoDetails,
                'cart_summary' => [
                    'subtotal_produk' => $cartSubtotal,
                    'diskon_diterapkan_rp' => $discountAmount,
                    'biaya_pengiriman_estimasi' => $shippingAfterPromo,
                    'total_akhir' => max(0, $totalAkhirDenganPromo)
                ]
            ]
        ];
    }
}



