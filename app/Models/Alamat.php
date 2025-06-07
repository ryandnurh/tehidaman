<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    protected $table = 'tb_alamat';
    protected $primaryKey = 'id_alamat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_alamat',
        'id_user',
        'label_alamat',
        'nama_penerima',
        'no_hp_penerima',
        'alamat',
        'latitude',
        'longitude',
        'status'
    ];

    public $timestamps = true;

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
