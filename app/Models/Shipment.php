<?php

namespace App\Models;

use App\Extensions\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use Watson\Validating\ValidatingTrait;

/**
 * Class Shipment
 * @package App\Models
 */
class Shipment extends Model
{
	use UuidTrait,
		ValidatingTrait,
		Auditable,
		SoftDeletes;

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
	protected $visible = ['id', 'width', 'height', 'length', 'weight'];

	/**
	 * @var array
	 */
	protected $fillable = ['id', 'width', 'height', 'length', 'weight'];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function category()
	{
		return $this->hasOne(ShipmentCategory::class);
	}

	public function order(){
		return $this->hasOne(Order::class);
	}

	/**
	 * @var array
	 */
	protected $rules = [
		'width' => 'required|numeric',
		'height' => 'required|numeric',
		'length' => 'required|numeric',
		'weight' => 'required|numeric',
	];
}
