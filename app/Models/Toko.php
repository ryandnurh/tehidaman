<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'tb_toko';

    protected $primaryKey = 'id_toko';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_toko',
        'nama_toko',
        'alamat_toko',
        'no_hp_toko',
        'foto_toko',
        'email_toko',
        'latitude',
        'longitude',
        'username_admin',
        'password_admin'
    ];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_toko', 'id_toko');
    }
}
