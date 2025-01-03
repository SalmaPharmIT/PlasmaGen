<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'name',
        'pin_code',
        'latitude',
        'longitude',
        'created_by',
        'modified_by',
        'modified_date',
    ];

    /**
     * Get the state that owns the city.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the entities associated with the city.
     */
    public function entities()
    {
        return $this->hasMany(Entity::class);
    }

    /**
     * Get the users associated with the city.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the user who created the city.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last modified the city.
     */
    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
