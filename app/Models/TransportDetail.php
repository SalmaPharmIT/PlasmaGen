<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transport_details';

    protected $fillable = [
        'tp_id',
        'vehicle_number',
        'driver_name',
        'contact_number',
        'alternative_contact_number',
        'email',
        'remarks',
        'created_by',
        'modified_by',
    ];

    // Define relationships
    public function tourPlan()
    {
        return $this->belongsTo(TourPlan::class, 'tp_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
