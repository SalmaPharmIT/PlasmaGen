<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodTestReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'minipool_id',
        'well_number',
        'od_value',
        'test_timestamp',
        'hbv_result',
        'hcv_result',
        'hiv_result',
        'final_result',
        'file_name',
        'operator',
        'instrument',
        'protocol',
        'test_type',
        'file_path',
        'summary',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'test_timestamp' => 'datetime',
        'summary' => 'array',
        'od_value' => 'decimal:4'
    ];

    // Constants for result types
    const RESULT_NONREACTIVE = 'nonreactive';
    const RESULT_BORDERLINE = 'borderline';
    const RESULT_REACTIVE = 'reactive';

    // Constants for test types
    const TEST_TYPE_HBV = 'HBV';
    const TEST_TYPE_HCV = 'HCV';
    const TEST_TYPE_HIV = 'HIV';

    /**
     * Get the creator of the report
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the last updater of the report
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the report
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get the result color class
     */
    public function getResultColorClass(): string
    {
        return match($this->final_result) {
            self::RESULT_NONREACTIVE => 'success',
            self::RESULT_BORDERLINE => 'info',
            self::RESULT_REACTIVE => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get the result text
     */
    public function getResultText(): string
    {
        return ucfirst($this->final_result);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->id();
            $model->save();
        });
    }
}
