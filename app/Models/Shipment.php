<?php

namespace App\Models;

use App\Extensions\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
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
	protected $visible = ['id', 'size_id', 'category_id', 'image_url'];

	/**
	 * @var array
	 */
	protected $fillable = ['size_id', 'category_id', 'image_url'];

	/**
	 * @var array
	 */
	protected $casts = ['image_url' => 'json'];

	/**
	 * @var array
	 */
	protected $rules = [
		'size_id' => 'required|integer|min:1',
		'category_id' => 'required|integer|min:1',
		'image_url' => 'required|array',
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
	 * @param $value
	 *
	 * @return string
	 */
	public function getImageUrlAttribute($value)
	{
		$value = json_decode($value);
		foreach ($value as $k => $file) {
			$value[$k] = 'https://s3.' . env('AWS_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/' . $file;
		}

		return $value;
	}

	/**
	 * @throws \Spatie\Image\Exceptions\InvalidManipulation
	 */
	public function registerMediaConversions()
	{
		$this->addMediaConversion('fitted')
			->fit(Manipulations::FIT_CROP, 400, 400);
	}
}
