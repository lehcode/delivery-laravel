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
	//const ROLE_RECIPIENT = 'recipient';
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
	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	/**
	 * @var array
	 */
	protected $casts = ['is_enabled' => 'boolean'];

	/**
	 * @var array
	 */
	protected $guarded = ['password', 'is_enabled'];

	/**
	 * @var array
	 */
	protected $visible = ['id', 'email', 'name', 'phone', 'photo', 'is_enabled', 'created_at', 'updated_at'];

	/**
	 * @var array
	 */
	protected $appends = ['profile'];

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
		'phone' => 'required|phone:AUTO,mobile|unique:users,phone',
		'password' => 'required|min:5'
	];

	/**
	 * @var array
	 */
	protected $validationMessages = [
		'name.required' => "User full name is required",
		'email.required' => "User email is required",
		'password.required' => "User password is required",
		'phone.required' => "User phone is required",
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

//			case self::ROLE_RECIPIENT:
//				return ProfileCustomer::find($this->id);
//				break;

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
