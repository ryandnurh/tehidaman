<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table = 'tb_keranjang';
    protected $primaryKey = 'id_keranjang';

    public $incrementing = false; 
    public $timestamps = true;
    public $keyType = 'string'; 

    protected $casts = [
        'id_keranjang' => 'string',
        'id_user' => 'string',
        'id_toko' => 'string',
        'id_produk' => 'string',
        'jumlah' => 'integer',
        'harga_total' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $fillable = [
        'id_keranjang',
        'id_user',
        'id_toko',
        'id_produk',
        'jumlah',
        'harga_total',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    public function getHargaTotalAttribute()
    {
        return $this->jumlah * $this->produk->harga;
    }


    
}
