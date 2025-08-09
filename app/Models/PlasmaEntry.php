<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlasmaEntry extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'pickup_date',
        'reciept_date',
        'grn_no',
        'blood_bank_id',
        'plasma_qty',
        'alloted_ar_no',
        'destruction_no',
        'remarks',
        'reject_reason',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'reciept_date' => 'date',
        'plasma_qty' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the module name for audit logs
     *
     * @return string
     */
    public function getAuditModule()
    {
        return 'Plasma Management';
    }

    /**
     * Get the section name for audit logs
     *
     * @return string
     */
    public function getAuditSection()
    {
        return 'Plasma Inward Entry';
    }

    public function bloodBank()
    {
        return $this->belongsTo(Entity::class, 'blood_bank_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
} 