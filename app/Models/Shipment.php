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
	protected $visible = ['id', 'size_id', 'category_id', 'image_url', 'price'];

	/**
	 * @var array
	 */
	protected $fillable = ['id', 'size_id', 'category_id', 'image_url', 'price'];

	/**
	 * @var array
	 */
	protected $rules = [
		'size_id'=>'required|integer',
		'category_id'=>'required|integer',
		'price'=>'required|numeric',
		'image_url'=>'string',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function category()
	{
		return $this->hasOne(ShipmentCategory::class, 'id', 'category_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function size()
	{
		return $this->hasOne(ShipmentSize::class, 'id', 'size_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function order()
	{
		return $this->hasOne(Order::class);
	}
}
