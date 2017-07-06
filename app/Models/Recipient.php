<?php

namespace App\Models;

use App\Models\User\Customer;
use Bosnadev\Database\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Watson\Validating\ValidatingTrait;

class Recipient extends Model implements AuditableInterface
{
	use UuidTrait,
		ValidatingTrait,
		AuditableTrait,
		SoftDeletes;

	/**
	 * @var array
	 */
	protected $fillable = ['id', 'name', 'email', 'notes'];

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
	protected $visible = ['id', 'name', 'phone', 'notes', 'created_at'];

	/**
	 * @var array
	 */
	protected $rules = [
		'name' => 'required|string|min:3',
		'phone' => 'required|phone:AUTO,mobile',
	];

	public function order()
	{
		return $this->hasMany(Order::class);
	}

	public function customer()
	{
		return $this->belongsTo(Customer::class, 'sender_id');
	}
}
