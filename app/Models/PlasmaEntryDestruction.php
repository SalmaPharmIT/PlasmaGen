<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlasmaEntryDestruction extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plasma_entries_destruction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pickup_date',
        'reciept_date',
        'grn_no',
        'blood_bank_id',
        'plasma_qty',
        'ar_no',
        'total_bag_val',
        'destruction_no',
        'donor_id',
        'donation_date',
        'blood_group',
        'bag_volume_ml',
        'reject_reason',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'pickup_date' => 'date',
        'reciept_date' => 'date',
        'plasma_qty' => 'decimal:2',
        'total_bag_val' => 'decimal:2',
        'donation_date' => 'date',
        'bag_volume_ml' => 'decimal:2',
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
        return 'Plasma Destruction Entry';
    }

    /**
     * Get the blood bank associated with the plasma entry.
     */
    public function bloodBank()
    {
        return $this->belongsTo(Entity::class, 'blood_bank_id');
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

    /**
     * Get the user who deleted the record.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
