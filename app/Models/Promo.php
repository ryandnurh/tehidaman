<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Promo extends Model
{
    protected $table = 'tb_promo';
    protected $primaryKey = 'id_promo';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id_promo',
        'nama_promo',
        'kode_promo',
        'deskripsi',
        'gambar',
        'jenis',
        'nilai_diskon',
        'minimal_pembelian',
        'maksimal_diskon',
        'tanggal_mulai',
        'tanggal_berakhir',
        'jumlah_penggunaan',
        'kuota_promo',
        'status',
        'syarat_ketentuan',
        'target_semua_user',
        'target_user_baru',
        'target_semua_produk',
        'id_kategori_target',
        'id_produk_target',
        'deskripsi_singkat'
    ];

    protected $casts = [
        'nilai_diskon' => 'float',
        'minimal_pembelian' => 'float',
        'maksimal_diskon' => 'float',
        'jumlah_penggunaan' => 'integer',
        'kuota_promo' => 'integer',
        'target_semua_user' => 'boolean',
        'target_user_baru' => 'boolean',  
        'target_semua_produk' => 'boolean',
        'tanggal_mulai' => 'datetime',    
        'tanggal_berakhir' => 'datetime',  
    ];

    protected $appends = ['gambar_url'];
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) {
            return null;
        }
        return Storage::disk('public')->url($this->gambar);
    }

    public function kategoriTarget()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori_target', 'id_kategori');
    }

    public function produkTarget()
    {
        return $this->belongsTo(Produk::class, 'id_produk_target', 'id_produk');
    }



    /**
     * Scope untuk mendapatkan promo yang aktif dan masih berlaku.
     */
    public function scopeActiveForUser($query)
    {
        $now = Carbon::now();
        return $query->where('status', 'aktif')
                     ->where('tanggal_mulai', '<=', $now)
                     ->where('tanggal_berakhir', '>=', $now)
                     ->where(function ($q) {
                         $q->whereNull('kuota_promo')
                           ->orWhereColumn('jumlah_penggunaan', '<', 'kuota_promo');
                     });
    }
}
