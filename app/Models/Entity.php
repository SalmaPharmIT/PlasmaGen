<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    public static $statuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_SUSPENDED => 'Suspended',
    ];

    protected $fillable = [
        'name',
        'entity_type_id',
        'entity_licence_number',
        'pan_id',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'address',
        'fax_number',
        'email',
        'mobile_no',
        'bank_account_number',
        'ifsc_code',
        'logo',
        'entity_customer_care_no',
        'gstin',
        'billing_address',
        'license_validity',
        'latitude',
        'longitude',
        'username',
        'password',
        'created_by',
        'modified_by',
        'modified_date',
        'account_status',
    ];

    /**
     * Get the entity type.
     */
    public function entityType()
    {
        return $this->belongsTo(EntityTypeMaster::class, 'entity_type_id');
    }

    /**
     * Get the country.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the users associated with the entity.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the user who created the entity.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last modified the entity.
     */
    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
