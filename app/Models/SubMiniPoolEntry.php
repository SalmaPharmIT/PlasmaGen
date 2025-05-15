<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubMiniPoolEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sub_mini_pool_entries';

    protected $fillable = [
        'mega_pool_no',
        'mini_pool_number',
        'sub_mini_pool_no',
        'timestamp',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Relationship with BarcodeEntry
    public function barcodeEntry()
    {
        return $this->belongsTo(BarcodeEntry::class, 'mega_pool_no', 'mega_pool_no');
    }
} 