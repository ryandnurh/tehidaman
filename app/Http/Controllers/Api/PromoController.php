<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo;

class PromoController extends Controller
{
    public function getPromo()
    {
        $promo = Promo::all();

        return response()->json([
            'message' => 'Berhasil mengambil data promo',
            'data' => $promo
        ]);
    }
}
