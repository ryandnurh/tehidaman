<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'tb_pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_pembayaran',
        'id_transaksi',
        'metode_pembayaran',
        'bukti_bayar',
        'status'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
