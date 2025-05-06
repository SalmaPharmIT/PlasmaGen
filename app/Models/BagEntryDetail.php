<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagEntryDetail extends Model
{
    use HasFactory;

    protected $table = 'bag_entries_details';

    protected $fillable = [
        'bag_entries_id',
        'no_of_bags',
        'bags_in_mini_pool',
        'donor_id',
        'donation_date',
        'blood_group',
        'bag_volume_ml',
        'tail_cutting',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'donation_date' => 'date',
        'no_of_bags' => 'integer',
        'bags_in_mini_pool' => 'integer',
        'bag_volume_ml' => 'integer'
    ];

    /**
     * Get the bag entry that owns this detail.
     */
    public function bagEntry()
    {
        return $this->belongsTo(BagEntry::class, 'bag_entries_id');
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