<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElisaTestReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'elisa_test_report';

    protected $fillable = [
        'mini_pool_id',
        'well_num',
        'od_value',
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
        'od_value' => 'decimal:2',
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
}
