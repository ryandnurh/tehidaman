<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorit extends Model
{
    protected $table = 'tb_favorit';
    protected $primaryKey = ['id_user', 'id_produk'];
    public $incrementing = false;
    public $timestamps = true;

    protected $keyType = 'string';

    protected $fillable = [
        'id_user',
        'id_produk',
    ];

    protected $casts = [
        'id_user' => 'string',
        'id_produk' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
