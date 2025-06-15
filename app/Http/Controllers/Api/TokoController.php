<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Toko;

class TokoController extends Controller
{
    public function getToko()
    {
        $toko = Toko::where('status_toko', 'buka')->get();

        return response()->json([
            'message' => 'Berhasil mengambil data toko',
            'data' => $toko
        ]);
    }

    public function findNearestToko(Request $request)
    {
        $validateData = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = $validateData['latitude'];
        $longitude = $validateData['longitude'];

        $toko = Toko::selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->where('status_toko', 'buka')
            ->orderBy('distance', 'asc')
            ->take(3)
            ->get();

        if (!$toko) {
            return response()->json([
                'message' => 'Tidak ada toko yang ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil menemukan toko terdekat',
            'data' => $toko
        ]);
    }

    public function getTokoById($id)
    {
        $toko = Toko::find($id);

        if (!$toko) {
            return response()->json([
                'message' => 'Toko tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data toko',
            'data' => $toko
        ]);
    }
}
