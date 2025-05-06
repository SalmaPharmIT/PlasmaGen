<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BagEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blood_bank_id',
        'work_station',
        'date',
        'pickup_date',
        'ar_no',
        'grn_no',
        'mega_pool_no',
        'total_mini_pool_volume',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date' => 'date',
        'pickup_date' => 'date',
        'total_mini_pool_volume' => 'decimal:2'
    ];

    /**
     * Get the blood bank that owns the bag entry.
     */
    public function bloodBank()
    {
        return $this->belongsTo(Entity::class, 'blood_bank_id');
    }

    /**
     * Get the details for this bag entry.
     */
    public function details()
    {
        return $this->hasMany(BagEntryDetail::class, 'bag_entries_id');
    }

    /**
     * Get the mini pools for this bag entry.
     */
    public function miniPools()
    {
        return $this->hasMany(BagEntryMiniPool::class, 'bag_entries_id');
    }

    /**
     * Get the user who created the bag entry.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the bag entry.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}
