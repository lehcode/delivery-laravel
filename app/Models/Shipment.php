<?php

namespace App\Models;

use App\Extensions\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Image\Manipulations;
use Watson\Validating\ValidatingTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * Class Shipment
 * @package App\Models
 */
class Shipment extends Model implements Auditable, HasMediaConversions
{
	use UuidTrait,
		ValidatingTrait,
		AuditableTrait,
		SoftDeletes,
		HasMediaTrait;

	const MEDIA_COLLECTION = 'shipments';
	
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
	protected $hidden = ['updated_at', 'deleted_at'];

	/**
	 * @var array
	 */
	protected $fillable = ['size_id', 'category_id'];

	/**
	 * @var array
	 */
	protected $rules = [
		'size_id' => 'required|integer|min:1',
		'category_id' => 'required|integer|min:1',
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

	/**
	 * @throws \Spatie\Image\Exceptions\InvalidManipulation
	 */
	public function registerMediaConversions()
	{
		$this->addMediaConversion('fitted')
			->fit(Manipulations::FIT_CROP, 400, 400);

		$this->addMediaConversion('thumb')
			->fit(Manipulations::FIT_CROP, 196, 196);
	}
}
