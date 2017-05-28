<?php

namespace App\Models;

use Bosnadev\Database\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use Watson\Validating\ValidatingTrait;

class Recipient extends Model
{
	use UuidTrait,
		ValidatingTrait,
		Auditable;

	/**
	 * @var array
	 */
	protected $fillable = ['name', 'email'];

	/**
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * @var array
	 */
	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	/**
	 * @var array
	 */
	protected $visible = ['id', 'name', 'phone', 'notes', 'created_at', 'updated_at'];

	/**
	 * @var array
	 */
	protected $rules = [
		'name' => 'required',
		'phone' => 'required|phone:AUTO,mobile',
	];

	/**
	 * @var array
	 */
	protected $validationMessages = [
		'name.required' => "Recipient name is required",
		'phone.required' => "Recipient phone is required",
	];
}
