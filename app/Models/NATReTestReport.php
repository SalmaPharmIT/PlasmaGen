<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class NATReTestReport extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'nat_re_test_report';

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
        'is_retest',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'timestamp',
        'deleted_at'
    ];

    protected $casts = [
        'is_retest' => 'boolean',
        'timestamp' => 'datetime'
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
        return 'NAT Re-test Report Upload';
    }

    // Scope for retest records
    public function scopeRetest($query)
    {
        return $query->where('is_retest', true);
    }

    // Scope for specific mini pool
    public function scopeByMiniPool($query, $miniPoolId)
    {
        return $query->where('mini_pool_id', $miniPoolId);
    }
}
