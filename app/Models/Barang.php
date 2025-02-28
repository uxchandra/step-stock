<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode', 
        'gambar',
        'nama_barang', 
        'jenis_id', 
        'size',
        'stok_minimum', 
        'stok_maximum', 
        'stok', 
        'nama_supplier',
        'price'
    ];
    protected $guarded = ['id'];
    protected $ignoreChangedAttributes = ['updated_at'];

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'jenis_id');
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function getStokTersediaAttribute()
    {
        return $this->stok - ($this->stok_terpesan ?? 0);
    }
}
