<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the states for the country.
     */
    public function states()
    {
        return $this->hasMany(State::class);
    }

    /**
     * Get the entities associated with the country.
     */
    public function entities()
    {
        return $this->hasMany(Entity::class);
    }

    /**
     * Get the users associated with the country.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
