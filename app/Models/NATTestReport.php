<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NATTestReport extends Model
{
    use SoftDeletes;

    protected $table = 'nat_test_report';

    protected $fillable = [
        'mini_pool_id',
        'hiv',
        'hbv',
        'hcv',
        'status',
        'result_time',
        'analyzer',
        'operator',
        'flags',
        'timestamp',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'timestamp',
        'deleted_at'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
} 