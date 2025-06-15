<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PromoService;
use Illuminate\Support\Facades\Auth;

class PromoController extends Controller
{
    protected $promoService;

    public function __construct(PromoService $promoService)
    {
        $this->promoService = $promoService;
    }

    public function getRecommendations(Request $request)
    {
        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*.id_produk' => 'required|string|exists:tb_produk,id_produk',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0'
        ]);
        
        $user = Auth::user();
        $recommendedPromos = $this->promoService->getRecommendedPromos($user, $validatedData['items']);

        if (empty($recommendedPromos)) {
            return response()->json(['message' => 'Tidak ada promo yang bisa digunakan.', 'data' => $recommendedPromos], 404);
        }
        // Format respons data
        $responseData = array_map(function($promoData, $index){
            return [
                'id_promo' => $promoData['promo_detail']->id_promo,
                'kode_promo' => $promoData['promo_detail']->kode_promo,
                'nama_promo' => $promoData['promo_detail']->nama_promo,
                'deskripsi_diskon_tampil' => $promoData['deskripsi_diskon_tampil'],
                'jenis' => $promoData['promo_detail']->jenis,
                'potensi_diskon_rp' => $promoData['potensi_diskon_rp'],
                'is_rekomendasi_terbaik' => ($index === 0 && ($promoData['potensi_diskon_rp'] > 0)),                
            ];
        }, $recommendedPromos, array_keys($recommendedPromos));


        return response()->json(['message' => 'Rekomendasi promo berhasil diambil.', 'data' => $responseData]);
    }

    public function applyPromoToCart(Request $request) // Sebenarnya ini lebih ke "preview" atau "validate"
    {
        $validatedData = $request->validate([
            'promo_identifier' => 'required|string',
            'identifier_type' => 'required|string|in:id,kode',
            'items' => 'required|array',
            'items.*.id_produk' => 'required|string|exists:tb_produk,id_produk',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $result = $this->promoService->validateAndPreviewChosenPromo(
            $user,
            $validatedData['promo_identifier'],
            $validatedData['identifier_type'],
            $validatedData['items'],
        );

        if (!$result['success']) {
            return response()->json(['message' => $result['message'], 'data' => $result['data'] ?? null], 422);
        }
        return response()->json(['message' => $result['message'], 'data' => $result['data']]);
    }
}