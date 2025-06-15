<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'tb_transaksi';
    protected $primaryKey = 'id_transaksi';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_transaksi',
        'id_user',
        'id_toko',
        'id_alamat',
        'total_harga',
        'id_promo_terpakai',
        'diskon',
        'harga_akhir',
        'catatan_pembeli',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko', 'id_toko');
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'id_alamat', 'id_alamat');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'id_promo_terpakai', 'id_promo');
    }

    public function detail()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    // dalam file app/Models/Transaksi.php

    /**
     * Mendefinisikan relasi bahwa satu Transaksi memiliki satu Pembayaran.
     */
    public function pembayaran()
    {
        // Pastikan namespace App\Models\Pembayaran sudah benar
        return $this->hasOne(Pembayaran::class, 'id_transaksi', 'id_transaksi');
    }
}