<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class ElisaTestReport extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

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

    // Define the module name for audit trail
    public function getAuditModule()
    {
        return 'Report Upload';
    }
}
