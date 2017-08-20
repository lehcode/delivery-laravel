<?php

namespace App\Models;

use App\Extensions\ProfileAttributeTrait;
use App\Models\User\Admin;
use App\Models\User\Carrier;
use App\Models\User\Customer;
use App\Models\User\Role;
use Jenssegers\Date\Date;
use App\Extensions\UuidTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Hash;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Watson\Validating\ValidatingTrait;
use Validator;

class User extends Authenticatable implements AuditableInterface, HasMediaConversions
{
	use LaratrustUserTrait,
		Notifiable,
		UuidTrait,
		SoftDeletes,
		ValidatingTrait,
		AuditableTrait,
		ProfileAttributeTrait,
		HasMediaTrait;

	const ROLE_ROOT = 'root';
	const ROLE_ADMIN = 'admin';
	const ROLE_CUSTOMER = 'customer';
	const ROLE_CARRIER = 'carrier';

	const PROFILE_IMAGE = 'photo';

	const UUID_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'username',
		'email',
		'phone',
		'is_enabled',
		'last_login',
		'password',
		'name',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = [
		'deleted_at',
		'created_at',
		'updated_at',
		'last_login'
	];

	/**
	 * @var array
	 */
	protected $casts = [
		'is_enabled' => 'boolean',
		'last_login' => 'datetime'
	];

	/**
	 * @var array
	 */
	//protected $guarded = ['password', 'is_enabled', 'remember_token'];

	/**
	 * @var array
	 */
	protected $visible = [
		'id',
		'email',
		'name',
		'username',
		'phone',
		'is_enabled',
		'created_at',
		'updated_at',
		'roles',
		'last_login',
	];

	/**
	 * @var array
	 */
	protected $appends = ['profile'];

	/**
	 * @var array
	 */
	protected $auditExclude = ['id', 'created_at', 'updated_at'];

	/**
	 * @var array
	 */
	protected $auditableEvents = ['deleted', 'updated', 'restored'];

	/**
	 * @var bool
	 */
	protected $throwValidationExceptions = true;

	/**
	 * @var array
	 */
	protected $rules = [
		'id' => 'required|regex:' . self::UUID_REGEX,
		'username' => 'required|string|min:3|unique:users,username',
		'name' => 'string|nullable|min:3|unique:users,email',
		'email' => 'email|nullable|unique:users,email',
		'password' => 'required|string|min:6',
		'phone' => 'nullable|phone:AUTO,mobile|unique:users,phone',
		'is_enabled' => 'nullable|boolean',
	];

	public function __construct($attributes = [])
	{

		parent::__construct($attributes);

		if (env('APP_DEBUG')) {
			$this->rules['password'] .= '|min:5';
		} else {
			$this->rules['password'] .= '|min:8';
		}
	}

	/**
	 * @param string $value
	 */
	public function setPasswordAttribute($value)
	{
		\Validator::make(['password' => $value], array_only($this->rules, ['password']))
			->validate();

		$this->attributes['password'] = \Hash::make($value);
	}

	/**
	 * @param Role $role
	 *
	 * @return $this
	 */
	public function attachRole($role)
	{
		$this->roles()->attach($this->getIdFor($role));
		$this->flushCache();

		return $this;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function customer()
	{
		return $this->hasOne(Customer::class, 'id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function carrier()
	{
		return $this->hasOne(Carrier::class, 'id');
	}

	/**
	 * @throws \Spatie\Image\Exceptions\InvalidManipulation
	 */
	public function registerMediaConversions()
	{
		$this->addMediaConversion('fitted')
			->fit(Manipulations::FIT_CROP, 400, 400);

		$this->addMediaConversion('thumb')
			->fit(Manipulations::FIT_CROP, 120, 160);
	}

}
