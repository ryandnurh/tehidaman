<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'tb_kategori';

    protected $primaryKey = 'id_kategori';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_kategori',
        'nama_kategori',
        'gambar',
        'deskripsi'
    ];

    protected $appends = ['gambar_kategori'];

    public function getGambarKategoriAttribute()
    {
        if ($this->attributes['gambar']) {
            $gambar = asset('storage/'.$this->attributes['gambar']);
            return $gambar;
        }
        return null;
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_kategori', 'id_kategori');
    }
}
