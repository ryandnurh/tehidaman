<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ProdukToko;

class ProdukController extends Controller
{
    public function getKategori()
    {
        $kategori = Kategori::all();

        return response()->json([
            'message' => 'Berhasil mengambil data kategori',
            'data' => $kategori
        ]);
    }

    public function getProduk(Request $request)
    {

        if ($request->has('id_toko')) {
            $id_toko = $request->id_toko;
            $produkQuery = Produk::whereHas('produkToko', function ($query) use ($id_toko) {
                $query->where('id_toko', $id_toko);
            });

            $produkQuery->with(['produkToko' => function ($query) use ($id_toko) {
                $query->where('id_toko', $id_toko);
            }]);

        } else {
            $produkQuery = Produk::select();
        }

        if ($request->has('id_kategori')) {
            $produkQuery->where('id_kategori', $request->id_kategori);
        }

        if ($request->has('id_produk')) {
            $produkQuery->where('id_produk', $request->id_produk);
        }

        $produk = $produkQuery->get();

        return response()->json([
            'message' => 'Berhasil mengambil data produk',
            'data' => $produk
        ]);
    }

    // public function getProdukById($id)
    // {
    //     $produk = Produk::with([
    //         'produkToko' => function ($query) use ($id) {
    //             $query->where('id_produk', $id);
    //         }
    //     ])->find($id);

    //     if (!$produk) {
    //         return response()->json([
    //             'message' => 'Produk tidak ditemukan',
    //             'data' => null
    //         ], 404);
    //     }

    //     return response()->json([
    //         'message' => 'Berhasil mengambil data produk',
    //         'data' => $produk
    //     ]);
    // }

    // public function getProdukByKategori($id)
    // {
    //     $produk = Produk::where('id_kategori', $id)->with('kategori')->get();

    //     if ($produk->isEmpty()) {
    //         return response()->json([
    //             'message' => 'Tidak ada produk ditemukan untuk kategori ini',
    //             'data' => []
    //         ], 404);
    //     }

    //     return response()->json([
    //         'message' => 'Berhasil mengambil data produk berdasarkan kategori',
    //         'data' => $produk
    //     ]);
    // }
}
