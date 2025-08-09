<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagEntryMiniPool extends Model
{
    use HasFactory;

    protected $table = 'bag_entries_mini_pools';

    protected $fillable = [
        'bag_entries_id',
        'bag_entries_detail_ids',
        'mini_pool_bag_volume',
        'mini_pool_number',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'bag_entries_detail_ids' => 'array',
        'mini_pool_bag_volume' => 'decimal:2'
    ];

    /**
     * Get the bag entry that owns this mini pool.
     */
    public function bagEntry()
    {
        return $this->belongsTo(BagEntry::class, 'bag_entries_id');
    }

    /**
     * Get the bag entry details associated with this mini pool.
     */
    public function bagEntryDetails()
    {
        return $this->belongsToMany(BagEntryDetail::class, null, null, null, 'bag_entries_detail_ids');
    }

    /**
     * Get the user who created the record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the record.
     */
    public function updater()
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
