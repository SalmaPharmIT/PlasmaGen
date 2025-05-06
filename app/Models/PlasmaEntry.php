<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlasmaEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reciept_date',
        'grn_no',
        'blood_bank_id',
        'alloted_ar_no',
        'destruction_no',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'reciept_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

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