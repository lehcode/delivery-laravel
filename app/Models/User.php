<?php

namespace App\Models;

use App\Extensions\UuidTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use Hash;
use Watson\Validating\ValidatingTrait;
use Validator;

class User extends Authenticatable
{
    use LaratrustUserTrait,
        Notifiable,
        UuidTrait,
        SoftDeletes,
        ValidatingTrait,
        Auditable;

    const ROLE_ROOT = 'root';
    const ROLE_ADMIN = 'admin';
    const ROLE_CUSTOMER = 'customer';
    const ROLE_DRIVER = 'driver';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'is_enabled'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $casts = ['is_enabled' => 'boolean'];

    protected $guarded = ['password', 'is_enabled'];

    protected $visible = ['id', 'email', 'phone', 'photo', 'is_enabled', 'created_at', 'updated_at'];

    protected $appends = ['profile'];

    protected $throwValidationExceptions = true;

    protected $rules = [
        'email' => 'required|email|unique:users,email',
        //'phone' => 'required|phone:AUTO,GB|unique:users,phone',
        'password' => 'required|min:5'
    ];

    /**
     * @return ProfileCustomer|ProfileDriver|null
     */
    public function getProfileAttribute()
    {
        switch ($this->roles()[0]->get('name')) {
            case self::ROLE_CUSTOMER:
                return ProfileCustomer::find($this->id);
                break;

            case self::ROLE_DRIVER:
                return ProfileDriver::find($this->id);
                break;

            case self::ROLE_ADMIN:
                return ProfileAdmin::find($this->id);
                break;

            default:
                return ProfileRoot::fund($this->id);
        }
    }

    /**
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $validator = Validator::make([
            'password' => $value
        ], array_only($this->rules, ['password']));

        $validator->validate();

        $this->attributes['password'] = Hash::make($value);
    }

}
