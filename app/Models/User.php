<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    public static $userStatuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_SUSPENDED => 'Suspended',
    ];

    const BLOOD_APLUS = 'A+';
    const BLOOD_AMINUS = 'A-';
    const BLOOD_ABPLUS = 'AB+';
    const BLOOD_ABMINUS = 'AB-';
    const BLOOD_BPLUS = 'B+';
    const BLOOD_BMINUS = 'B-';
    const BLOOD_OPLUS = 'O+';
    const BLOOD_OMINUS = 'O-';

    public static $userBloodGroups = [
        self::BLOOD_APLUS => 'A+',
        self::BLOOD_AMINUS => 'A-',
        self::BLOOD_ABPLUS => 'AB+',
        self::BLOOD_ABMINUS => 'AB-',
        self::BLOOD_BPLUS => 'B+',
        self::BLOOD_BMINUS => 'B-',
        self::BLOOD_OPLUS => 'O+',
        self::BLOOD_OMINUS => 'O-',
    ];


    const STATUS_GENDER_MALE = 'male';
    const STATUS_GENDER_FEMALE = 'female';
    const STATUS_GENDER_OTHER = 'other';

    public static $userGender = [
        self::STATUS_GENDER_MALE => 'Male',
        self::STATUS_GENDER_FEMALE => 'Female',
        self::STATUS_GENDER_OTHER => 'Other',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'role_id',
        'entity_id',
        'email',
        'mobile',
        'gender',
        'account_status',
        'created_by',
        'modified_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

     /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    /**
     * Get the entity associated with the user.
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the state associated with the user.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city associated with the user.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the country associated with the user through state.
     */
    public function country()
    {
        return $this->hasOneThrough(Country::class, State::class, 'id', 'id', 'state_id', 'country_id');
    }

     // Define relationships if necessary
     public function entityType()
     {
         return $this->belongsTo(EntityType::class);
     }

    public function getProfileImage()
    {
        return $this->profile_pic ? asset($this->profile_pic) :  asset('assets/img/default-profile-img.png');
    }
}
