<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubMiniPoolElisaTestReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sub_mini_pool_elisa_test_report';

    protected $fillable = [
        'sub_mini_pool_id',
        'mini_pool_number',
        'well_num',
        'od_value',
        'ratio',
        'result_time',
        'hbv',
        'hcv',
        'hiv',
        'final_result',
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

    /**
     * Get the user who created the record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the record
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}

