<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityTypeMaster extends Model
{
    use HasFactory;

    protected $table = 'entity_type_master';

    protected $fillable = ['entity_name', 'created_user', 'modified_user'];

    /**
     * Get the entities of this type.
     */
    public function entities()
    {
        return $this->hasMany(Entity::class, 'entity_type_id');
    }
}
