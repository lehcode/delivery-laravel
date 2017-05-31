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
	protected $fillable = ['name', 'description'];

	/**
	 * @var array
	 */
	protected $rules = [
		'name' => 'required',
		'description' => 'string',
	];

	/**
	 * @var array
	 */
	protected $validationMessages = [
		'name.required' => "Recipient name is required",
		'phone.required' => "Recipient phone is required",
	];
	
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function shipment()
	{
		return $this->belongsTo(Shipment::class);
	}
}
