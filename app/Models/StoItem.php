<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sto_event_id',
        'barang_id',
        'stok_sistem',
        'stok_aktual',
        'selisih',
        'catatan',
        'scanned_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'stok_sistem' => 'integer',
        'stok_aktual' => 'integer',
        'selisih' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically calculate selisih (difference) before saving
        static::saving(function ($stoItem) {
            $stoItem->selisih = $stoItem->stok_sistem - $stoItem->stok_aktual;
        });
    }

    /**
     * Get the event that this item belongs to.
     */
    public function stoEvent()
    {
        return $this->belongsTo(StoEvent::class, 'sto_event_id');
    }

    /**
     * Get the barang that this STO item refers to.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Get the user who scanned this item.
     */
    public function scannedByUser()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    /**
     * Scope a query to only include items with discrepancies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasDiscrepancy($query)
    {
        return $query->where('selisih', '!=', 0);
    }

    /**
     * Check if this item has a stock discrepancy.
     *
     * @return bool
     */
    public function hasDiscrepancy()
    {
        return $this->selisih != 0;
    }

    /**
     * Get the status description of the discrepancy.
     *
     * @return string
     */
    public function getDiscrepancyStatusAttribute()
    {
        if ($this->selisih == 0) {
            return 'Sesuai';
        } elseif ($this->selisih > 0) {
            return 'Kurang';
        } else {
            return 'Lebih';
        }
    }
}