<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BagEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blood_centre',
        'work_station',
        'date',
        'pickup_date',
        'ar_no',
        'grn_no',
        'mega_pool_no',
        'bag_details',
        'total_volume',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date' => 'date',
        'pickup_date' => 'date',
        'bag_details' => 'json'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

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
