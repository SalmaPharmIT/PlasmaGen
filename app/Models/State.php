<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = ['country_id', 'name'];

    /**
     * Get the country that owns the state.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the cities for the state.
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get the users associated with the state.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the entities associated with the state.
     */
    public function entities()
    {
        return $this->hasMany(Entity::class);
    }
}
