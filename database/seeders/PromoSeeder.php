<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promo;
use Carbon\Carbon\CarbonInterface;
use Carbon\Carbon;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Menggunakan tanggal sekarang sebagai acuan agar promo dinamis
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $prevMonthEnd = $now->copy()->subMonth()->endOfMonth();
        
        Promo::insert([
            [
                'id_promo' => 'PROMO001',
                'nama_promo' => 'Diskon Selamat Datang!',
                'kode_promo' => 'BARUHEMAT15',
                'deskripsi' => 'Diskon 15% khusus untuk pengguna baru di pembelian pertama. Min. belanja Rp 75.000.',
                'gambar' => 'promos/new_user_promo.png',
                'jenis' => 'persentase',
                'nilai_diskon' => 15, // Artinya 15%
                'minimal_pembelian' => 75000,
                'maksimal_diskon' => 20000, // Potongan maksimal Rp 20.000
                'tanggal_mulai' => $startOfMonth,
                'tanggal_berakhir' => $endOfMonth,
                'jumlah_penggunaan' => 0,
                'kuota_promo' => 1000,
                'status' => 'aktif',
                'syarat_ketentuan' => 'Hanya berlaku untuk satu kali penggunaan per pengguna baru.',
                'target_semua_user' => false,
                'target_user_baru' => true,
                'target_semua_produk' => true,
                'id_kategori_target' => null,
                'id_produk_target' => null,
                'deskripsi_singkat' => 'Diskon 15% Pengguna Baru!',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_promo' => 'PROMO002',
                'nama_promo' => 'Spesial Teh Melati',
                'kode_promo' => 'MELATI5RB',
                'deskripsi' => 'Dapatkan potongan langsung Rp 5.000 untuk setiap pembelian Teh Melati Super.',
                'gambar' => 'promos/tea_promo.png',
                'jenis' => 'nominal',
                'nilai_diskon' => 5000, // Artinya potongan Rp 5.000
                'minimal_pembelian' => 0,
                'maksimal_diskon' => null,
                'tanggal_mulai' => $startOfMonth,
                'tanggal_berakhir' => $endOfMonth,
                'jumlah_penggunaan' => 12,
                'kuota_promo' => 500,
                'status' => 'aktif',
                'syarat_ketentuan' => 'Berlaku khusus untuk produk Teh Melati Super.',
                'target_semua_user' => true,
                'target_user_baru' => false,
                'target_semua_produk' => false,
                'id_kategori_target' => null,
                'id_produk_target' => 'PROD001', // Ganti dengan ID produk teh melati Anda
                'deskripsi_singkat' => 'Potongan Rp 5.000 Teh Melati',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_promo' => 'PROMO003',
                'nama_promo' => 'Pesta Teh Herbal',
                'kode_promo' => 'SEHAT20',
                'deskripsi' => 'Diskon 20% untuk semua produk dalam kategori Teh Herbal. Min. belanja produk herbal Rp 40.000.',
                'gambar' => 'promos/herbal_promo.png',
                'jenis' => 'persentase',
                'nilai_diskon' => 20, // Artinya 20%
                'minimal_pembelian' => 40000,
                'maksimal_diskon' => 25000,
                'tanggal_mulai' => $startOfMonth,
                'tanggal_berakhir' => $now->copy()->addDays(10), // Berlaku hingga 10 hari dari sekarang
                'jumlah_penggunaan' => 45,
                'kuota_promo' => 200,
                'status' => 'aktif',
                'syarat_ketentuan' => 'Minimal pembelian Rp 40.000 dihitung dari subtotal produk dalam kategori Teh Herbal.',
                'target_semua_user' => true,
                'target_user_baru' => false,
                'target_semua_produk' => false,
                'id_kategori_target' => 'KAT002', // Ganti dengan ID kategori teh herbal Anda
                'id_produk_target' => null,
                'deskripsi_singkat' => 'Diskon 20% Teh Herbal!',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_promo' => 'PROMO004',
                'nama_promo' => 'Promo Akhir Pekan',
                'kode_promo' => 'WEEKENDSERU',
                'deskripsi' => 'Potongan Rp 10.000 untuk semua pesanan di akhir pekan! Min. belanja Rp 100.000.',
                'gambar' => 'promos/weekend_promo.png',
                'jenis' => 'nominal',
                'nilai_diskon' => 10000,
                'minimal_pembelian' => 100000,
                'maksimal_diskon' => null,
                'tanggal_mulai' => $now->copy()->startOfWeek(\Carbon\CarbonInterface::FRIDAY)->startOfDay(), // Mulai Jumat ini
                'tanggal_berakhir' => $now->copy()->endOfWeek(\Carbon\CarbonInterface::SUNDAY)->endOfDay(), // Berakhir Minggu ini
                'jumlah_penggunaan' => 0,
                'kuota_promo' => null, // Kuota tak terbatas
                'status' => 'aktif',
                'syarat_ketentuan' => 'Hanya berlaku hari Jumat, Sabtu, Minggu.',
                'target_semua_user' => true,
                'target_user_baru' => false,
                'target_semua_produk' => true,
                'id_kategori_target' => null,
                'id_produk_target' => null,
                'deskripsi_singkat' => 'Potongan Weekend Rp 10.000',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_promo' => 'PROMO005',
                'nama_promo' => 'Diskon Kilat Bulan Mei (Kadaluarsa)',
                'kode_promo' => 'MEIHEMAT',
                'deskripsi' => 'Promo kilat bulan Mei yang sudah berakhir.',
                'gambar' => null,
                'jenis' => 'persentase',
                'nilai_diskon' => 30,
                'minimal_pembelian' => 100000,
                'maksimal_diskon' => 30000,
                'tanggal_mulai' => $prevMonthEnd->copy()->subDays(5),
                'tanggal_berakhir' => $prevMonthEnd, // Tanggal berakhir di bulan lalu
                'jumlah_penggunaan' => 120,
                'kuota_promo' => 200,
                'status' => 'kadaluarsa',
                'syarat_ketentuan' => 'Promo sudah tidak berlaku.',
                'target_semua_user' => true,
                'target_user_baru' => false,
                'target_semua_produk' => true,
                'id_kategori_target' => null,
                'id_produk_target' => null,
                'deskripsi_singkat' => 'Promo Mei',
                'created_at' => $prevMonthEnd->copy()->subDays(10),
                'updated_at' => $prevMonthEnd->copy()->addDay(),
            ],
            [
                'id_promo' => 'PROMO006',
                'nama_promo' => 'Promo Terbatas (Habis)',
                'kode_promo' => 'CEPATDAPAT',
                'deskripsi' => 'Diskon Rp 25.000 untuk 100 pembeli pertama!',
                'gambar' => null,
                'jenis' => 'nominal',
                'nilai_diskon' => 25000,
                'minimal_pembelian' => 120000,
                'maksimal_diskon' => null,
                'tanggal_mulai' => $startOfMonth,
                'tanggal_berakhir' => $endOfMonth,
                'jumlah_penggunaan' => 100, // Kuota terpakai = kuota promo
                'kuota_promo' => 100, // Kuota promo
                'status' => 'habis',
                'syarat_ketentuan' => 'Kuota penggunaan promo ini sudah habis.',
                'target_semua_user' => true,
                'target_user_baru' => false,
                'target_semua_produk' => true,
                'id_kategori_target' => null,
                'id_produk_target' => null,
                'deskripsi_singkat' => 'Promo Habis',
                'created_at' => $startOfMonth,
                'updated_at' => $now,
            ],
            [
                'id_promo' => 'PROMO007',
                'nama_promo' => 'Diskon Otomatis Teh Jahe',
                'kode_promo' => null, // Promo tanpa kode
                'deskripsi' => 'Beli Teh Jahe Hangat, langsung dapat potongan Rp 2.000 di keranjang.',
                'gambar' => null,
                'jenis' => 'nominal',
                'nilai_diskon' => 2000,
                'minimal_pembelian' => 0,
                'maksimal_diskon' => null,
                'tanggal_mulai' => $startOfMonth,
                'tanggal_berakhir' => $endOfMonth,
                'jumlah_penggunaan' => 22,
                'kuota_promo' => null,
                'status' => 'aktif',
                'syarat_ketentuan' => 'Berlaku otomatis untuk produk Teh Jahe Hangat.',
                'target_semua_user' => true,
                'target_user_baru' => false,
                'target_semua_produk' => false,
                'id_kategori_target' => null,
                'id_produk_target' => 'PROD002', // Ganti dengan ID produk teh jahe Anda
                'deskripsi_singkat' => 'Otomatis Diskon Teh Jahe',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}