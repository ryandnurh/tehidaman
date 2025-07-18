<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Favorit;
use App\Models\Produk;
use App\Models\Keranjang;
use Str;

class UserController extends Controller
{

    public function getUser(Request $request)
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        

        return response()->json([
            'message' => 'Berhasil mengambil data user',
            'data' => $user,
        ], 200);
    }

    public function updateUser(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'email|max:100|unique:tb_users,email,'
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();

if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('profile', 'public');
        $user->foto = $path;
    }

        // Update data user
        $user->update($request->all());

        return response()->json([
            'message' => 'Data user berhasil diperbarui',
            'data' => $user,
        ]);
    }







    //alamat

    public function tambahAlamat(Request $request)
    {
        // Validasi input
        $request->validate([
            'alamat' => 'required|string',
            'detail_alamat' => 'required|string',
            'label_alamat' => 'required|string|max:100',
            'nama_penerima' => 'required|string|max:50',
            'no_hp_penerima' => 'required|string|max:15',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'in:utama,tambahan',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();
        $alamatUtama = $user->alamat()->where('status', 'utama')->first();

        if ($request->has('status') && $request->status === 'utama') {
            // Set semua alamat ke tambahan jika statusnya utama
            $user->alamat()->update(['status' => 'tambahan']);
        } else if (!$alamatUtama) {
            $request->merge(['status' => 'utama']);
        }

        // Tambahkan alamat baru ke user
        $user->alamat()->create([
            'id_alamat' => 'AL' . Str::random(8),
            'alamat' => $request->alamat,
            'detail_alamat' => $request->detail_alamat,
            'label_alamat' => $request->label_alamat,
            'nama_penerima' => $request->nama_penerima,
            'no_hp_penerima' => $request->no_hp_penerima,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $request->status ?? 'tambahan',
            'id_user' => $user->id_user,
        ]);

        return response()->json([
            'message' => 'Alamat berhasil ditambahkan',
            'data' => $user->alamat,
        ], 201);
    }




    public function getAlamat(Request $request)
    {

        if ($request->has('id_alamat')) {
            $id_alamat = $request->id_alamat;
            // Ambil alamat berdasarkan id_alamat
            $alamat = auth()->user()->alamat()->where('id_alamat', $id_alamat)->first();

            if (!$alamat) {
                return response()->json(['message' => 'Alamat tidak ditemukan'], 404);
            }

        } else {
            // Ambil user yang sedang login
            $user = auth()->user();

            // Ambil semua alamat yang dimiliki user
            $alamat = $user->alamat;

            if (!$alamat) {
                return response()->json(['message' => 'Alamat tidak ditemukan'], 404);
            }

        }

        return response()->json([
            'message' => 'Berhasil mengambil data alamat',
            'data' => $alamat,
        ], 200);

    }




    public function editAlamat(Request $request)
    {
        // Validasi input

        // Ambil user yang sedang login
        $user = auth()->user();

        // Cari alamat berdasarkan id_alamat
        $alamat = $user->alamat()->where('id_alamat', $request->id_alamat)->first();

        if (!$alamat) {
            return response()->json(['message' => 'Alamat tidak ditemukan'], 404);
        }

        // Jika ingin mengubah jadi alamat utama
        if ($request->has('status') && $request->status === 'utama') {
            // Semua alamat user di-set ke tambahan
            $user->alamat()->update(['status' => 'tambahan']);
        }

        // Update alamat
        $alamat->update($request->all());

        return response()->json([
            'message' => 'Alamat berhasil diperbarui',
            'data' => $alamat,
        ]);
    }



    
public function deleteAlamat(Request $request)
{
    $user = auth()->user();

    // Cari alamat berdasarkan id_alamat
    $alamat = $user->alamat()->where('id_alamat', $request->id_alamat)->first();

    if (!$alamat) {
        return response()->json(['message' => 'Alamat tidak ditemukan'], 404);
    }

    $isUtama = $alamat->status === 'utama';

    // Hapus alamat
    $alamat->delete();

    // Jika yang dihapus adalah utama, dan masih ada alamat lain → jadikan salah satunya sebagai utama
    if ($isUtama) {
        $alamatLain = $user->alamat()->first(); // ambil salah satu alamat lain, jika ada
        if ($alamatLain) {
            $alamatLain->update(['status' => 'utama']);
        }
    }

    return response()->json([
        'message' => 'Alamat berhasil dihapus',
        'data' => $user->alamat,
    ]);
}








    //favorit

    public function tambahFavorit(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_produk' => 'required|string|max:10',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();

        // Cek apakah produk sudah ada di favorit
        $favorite = $user->favorit()->where('id_produk', $request->id_produk)->first();

        if ($favorite) {
            return response()->json(['message' => 'Produk sudah ada di favorit'], 400);
        }

        // Tambahkan produk ke favorit
        $user->favorit()->create([
            'id_user' => $user->id_user,
            'id_produk' => $request->id_produk,
        ]);

        return response()->json(['message' => 'Produk berhasil ditambahkan ke favorit'], 201);
    }


    public function deleteFavorit(Request $request)
    {

        // Validasi input
        $request->validate([
            'id_produk' => 'required|string|max:10',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();

        // Cek apakah produk ada di favorit
        $favorite = Favorit::where('id_produk', $request->id_produk)->where('id_user', $user->id_user)->delete();

        if (!$favorite) {
            return response()->json(['message' => 'Produk tidak ditemukan di favorit'], 404);
        }

        return response()->json(['message' => 'Produk berhasil dihapus dari favorit'], 200);
    }

    public function getFavorit(Request $request)
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Ambil semua produk favorit yang dimiliki user
        $favorit = Favorit::select(
            'tb_favorit.id_produk', 'tb_produk.nama_produk', 'tb_produk.harga', 'tb_produk.gambar_produk')
        ->join('tb_produk', 'tb_favorit.id_produk', '=', 'tb_produk.id_produk')
        ->where('id_user', $user->id_user)
        ->get();

        if ($favorit->isEmpty()) {
            return response()->json(['message' => 'Tidak ada produk favorit'], 404);
        }

        $favorit = $favorit->map(function ($item) {
            $item['gambar'] = asset('storage/' . $item->gambar_produk);
            return $item;
        });

        return response()->json([
            'message' => 'Berhasil mengambil data produk favorit',
            'data' => $favorit,
        ], 200);
    }







    //keranjang
    public function tambahKeranjang(Request $request)
    {

        $request->validate([
            'id_produk' => 'required|string|max:10',
            'id_toko' => 'required|string|max:10',
        ]);

        $user = auth()->user();

        $hargaProduk = Produk::where('id_produk', $request->id_produk)->value('harga');
        if (!$hargaProduk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // Cek apakah produk sudah ada di keranjang
        $keranjang = $user->keranjang()->where('id_produk', $request->id_produk)->first();
        if ($keranjang) {
            // Jika produk sudah ada, tambahkan jumlahnya
            $keranjang->jumlah += $request->jumlah ?? 1;
            $keranjang->harga_total = $hargaProduk * $keranjang->jumlah;
            $keranjang->save();
            return response()->json(['message' => 'Jumlah produk di keranjang berhasil ditambahkan'], 200);
        }

        $total = $hargaProduk * $request->jumlah;

        $user->keranjang()->create([
            'id_keranjang' => 'KR' . Str::random(8),
            'id_user' => $user->id_user,
            'id_toko' => $request->id_toko,
            'id_produk' => $request->id_produk,
            'jumlah' => $request->jumlah ?? 1,
            'harga_total' => $total,

        ]);
        return response()->json(['message' => 'Produk berhasil ditambahkan ke keranjang'], 201);
    }


    public function getKeranjang(Request $request)
    {
        $user = auth()->user();

        $keranjang = Keranjang::select(
            'tb_keranjang.*',
            'tb_produk.nama_produk',
            'tb_produk.harga',
            'tb_produk.gambar_produk'
        )
            ->join('tb_produk', 'tb_keranjang.id_produk', '=', 'tb_produk.id_produk')
            ->where('tb_keranjang.id_user', $user->id_user)
            ->get();


        if ($keranjang->isEmpty()) {
            return response()->json(['message' => 'Tidak ada produk di keranjang'], 404);
        }

        $totalHarga = DB::selectOne("SELECT hitung_total_harga_keranjang(?) AS total", [$user->id_user])->total;


        $keranjang = $keranjang->map(function ($item) {
            $item['gambar'] = asset('storage/' . $item->gambar_produk);
            return $item;
        });


        $data = [
            'keranjang' => $keranjang,
            'total_harga' => $totalHarga
        ];

        return response()->json([
            'message' => 'Berhasil mengambil data keranjang',
            'data' => $data
        ], 200);
    }

    public function editKeranjang(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_keranjang' => 'required|string|max:10',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();

        // Cari keranjang berdasarkan id_keranjang
        $keranjang = $user->keranjang()->where('id_keranjang', $request->id_keranjang)->first();

        if (!$keranjang) {
            return response()->json(['message' => 'Keranjang tidak ditemukan'], 404);
        }

        // Update jumlah dan harga total
        $hargaProduk = Produk::where('id_produk', $keranjang->id_produk)->value('harga');
        if (!$hargaProduk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $keranjang->jumlah = $request->jumlah;
        $keranjang->harga_total = $hargaProduk * $request->jumlah;
        $keranjang->save();

        return response()->json([
            'message' => 'Keranjang berhasil diperbarui',
            'data' => $keranjang,
        ]);
    }


    public function deleteKeranjang(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_keranjang' => 'required|string|max:10',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();

        // Cari keranjang berdasarkan id_keranjang
        $keranjang = $user->keranjang()->where('id_keranjang', $request->id_keranjang)->first();

        if (!$keranjang) {
            return response()->json(['message' => 'Keranjang tidak ditemukan'], 404);
        }

        // Hapus keranjang
        $keranjang->delete();

        return response()->json([
            'message' => 'Keranjang berhasil dihapus',
            'data' => $user->keranjang,
        ], 200);
    }

}
