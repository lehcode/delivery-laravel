<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

/**
 * Class ShipmentSize
 * @package App\Models
 */
class ShipmentSize extends Model
{
	use ValidatingTrait,
		SoftDeletes;

	/**
	 * @var array
	 */
	protected $dates = [
		'deleted_at',
		'created_at',
		'updated_at'
	];

	/**
	 * @var array
	 */
	protected $fillable = [
		'name',
		'length',
		'width',
		'height',
		'weight',
		'description',
	];

	/**
	 * @var array
	 */
	protected $hidden = ['created_at', 'updated_at'];

	/**
	 * @var array
	 */
	protected $rules = [
		'name' => 'required|string',
		'length' => 'required|integer',
		'width' => 'required|integer',
		'height' => 'required|integer',
		'weight' => 'required|numeric',
		'description' => 'string',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function shipment()
	{
		return $this->belongsTo(Shipment::class, 'size_id');
	}
}
