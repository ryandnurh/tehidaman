<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukToko extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'tb_produk_toko';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_produk',
        'id_toko',
        'stok',
        'status'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko', 'id_toko');
    }
}
