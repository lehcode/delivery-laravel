<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

/**
 * Class ShipmentCategory
 * @package App\Models
 */
class ShipmentCategory extends Model
{
	use ValidatingTrait,
		SoftDeletes;
	
	/**
	 * @var array
	 */
	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	/**
	 * @var array
	 */
	protected $hidden = ['created_at', 'updated_at'];

	/**
	 * @var array
	 */
	protected $rules = [
		'name' => 'required|string',
		'description' => 'string',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function shipment()
	{
		return $this->belongsTo(Shipment::class, 'category_id');
	}
}
