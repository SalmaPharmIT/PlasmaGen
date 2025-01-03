<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import the SoftDeletes trait

class TourPlan extends Model
{
    use HasFactory, SoftDeletes; // Use the SoftDeletes trait

    // Specify the table if not following Laravel's naming convention
    protected $table = 'tour_plan';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'blood_bank_id',
        'employee_id',
        'entity_id',
        'visit_date',
        'latitude',
        'longitude',
        'remarks',
        'status',
        'client_type',
        'created_by',
        'modified_by',
    ];

    // Optionally, define dates to be treated as Carbon instances
    protected $dates = [
        'visit_date',
        'deleted_at', // Include deleted_at for soft deletes
    ];

    // Define relationships if any
    public function bloodBank()
    {
        return $this->belongsTo(Entity::class, 'blood_bank_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
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
