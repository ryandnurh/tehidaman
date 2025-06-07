<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function getKategori()
    {
        $kategori = \App\Models\Kategori::all();

        return response()->json([
            'message' => 'Berhasil mengambil data kategori',
            'data' => $kategori
        ]);
    }

    public function getProduk()
    {
        $produk = \App\Models\Produk::with('kategori')->get();

        return response()->json([
            'message' => 'Berhasil mengambil data produk',
            'data' => $produk
        ]);
    }

    public function getProdukById($id)
    {
        $produk = \App\Models\Produk::with('kategori')->find($id);

        if (!$produk) {
            return response()->json([
                'message' => 'Produk tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data produk',
            'data' => $produk
        ]);
    }
    
    public function getProdukByKategori($id)
    {
        $produk = \App\Models\Produk::where('kategori_id', $id)->with('kategori')->get();

        if ($produk->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada produk ditemukan untuk kategori ini',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data produk berdasarkan kategori',
            'data' => $produk
        ]);
    }
}
