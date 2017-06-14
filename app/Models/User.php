<?php

namespace App\Models;

use App\Extensions\ProfileAttributeTrait;
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
use Watson\Validating\ValidatingTrait;
use Validator;

class User extends Authenticatable implements AuditableInterface
{
	use LaratrustUserTrait,
		Notifiable,
		UuidTrait,
		SoftDeletes,
		ValidatingTrait,
		AuditableTrait,
		ProfileAttributeTrait;

	const ROLE_ROOT = 'root';
	const ROLE_ADMIN = 'admin';
	const ROLE_CUSTOMER = 'customer';
	const ROLE_CARRIER = 'carrier';

	const PROFILE_IMAGE = 'picture';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'phone', 'password', 'is_enabled', 'roles', 'last_login'];

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
	protected $dates = ['deleted_at', 'created_at', 'updated_at', 'last_login'];

	/**
	 * @var array
	 */
	protected $casts = ['is_enabled' => 'boolean', 'last_login'=>'datetime'];

	/**
	 * @var array
	 */
	protected $guarded = ['password', 'is_enabled', 'remember_token'];

	/**
	 * @var array
	 */
	protected $visible = ['id', 'email', 'name', 'phone', 'photo', 'is_enabled', 'created_at', 'updated_at', 'roles', 'last_login'];

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
		'name' => 'required',
		'email' => 'required|email|unique:users,email',
		//'phone' => 'required|phone:AUTO,mobile|unique:users,phone',
		'password' => 'required|min:5'
	];

	/**
	 * @var array
	 */
	protected $validationMessages = [
		'name.required' => "User full name is required",
		'email.required' => "User email is required",
		'email.unique' => "Email must be unique",
		'email.email' => "Email has wrong format",
		'password.required' => "User password is required",
		//'phone.required' => "User phone is required",
		'phone.phone' => "User phone is wrong",
	];

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

	/**
	 * @param Role $role
	 *
	 * @return $this
	 */
	public function attachRole(Role $role)
	{
		$this->roles()->attach($this->getIdFor($role));
		$this->flushCache();

		return $this;
	}

	public function customer()
	{
		return $this->hasOne(Customer::class, 'id');
	}

	public function carrier()
	{
		return $this->hasOne(Carrier::class, 'id');
	}

}
