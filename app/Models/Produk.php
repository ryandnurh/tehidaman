<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Produk extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'tb_produk';

    protected $primaryKey = 'id_produk';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_produk',
        'id_kategori',
        'gambar_produk',
        'nama_produk',
        'deskripsi',
        'harga',
        'jumlah_terjual'
    ];

    protected $appends = ['gambar_url'];

    public function getGambarUrlAttribute()
    {
        if (!$this->gambar_produk) {
            return null;
        }
        return Storage::disk('public')->url($this->gambar_produk);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function produkToko()
    {
        return $this->hasMany(ProdukToko::class, 'id_produk', 'id_produk');
    }
}
