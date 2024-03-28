<?php

namespace App\Models;

use App\Extensions\UuidTrait;
use App\Models\User\Carrier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Watson\Validating\ValidatingTrait;
use App\Extensions\RfcDateTrait;

/**
 * Class Trip
 * @package App\Models
 */
class Trip extends Model implements AuditableInterface
{

	use UuidTrait,
		SoftDeletes,
		ValidatingTrait,
		AuditableTrait,
		RfcDateTrait;

	/**
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * @var array
	 */
	protected $dates = [
		'deleted_at',
		'created_at',
		'updated_at',
		'departure_date'
	];

	/**
	 * @var array
	 */
	protected $fillable = [
		'id',
		'payment_type_id',
		'carrier_id',
		'from_city_id',
		'to_city_id',
		'departure_date',
	];

	/**
	 * @var array
	 */
	protected $hidden = ['updated_at', 'deleted_at'];

	/**
	 * @var array
	 */
	protected $rules = [
		//'payment_type_id' => 'required|integer|exists:payment_types,id',
		'carrier_id' => 'required|string|exists:carriers,id',
		'from_city_id' => 'required|integer|exists:cities,id',
		'to_city_id' => 'required|integer|exists:cities,id',
		'departure_date' => 'required|date'
	];

	/**
	 * @var array
	 */
	protected $geofields = ['geo_start', 'geo_end'];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function carrier()
	{
		return $this->belongsTo(Carrier::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function fromCity()
	{
		return $this->hasOne(City::class, 'id', 'from_city_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function destinationCity()
	{
		return $this->hasOne(City::class, 'id', 'to_city_id');
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function getDepartureDateAttribute($value){
		return $this->rfcDate($value);
	}

	/**
	 * @param array $value
	 */
	public function setGeoStartAttribute(array $value)
	{
		$this->attributes['geo_start'] = \DB::raw("ST_GeomFromText('POINT(" . implode(' ', $value) . ")')");
	}

	public function getGeoStartAttribute($value)
	{
		return Order::formatLocationAttribute($value);
	}

	/**
	 * @param $value
	 *
	 * @return array
	 */
	public function getGeoEndAttribute($value)
	{
		return Order::formatLocationAttribute($value);
	}

	/**
	 * @param array $value
	 */
	public function setGeoEndAttribute(array $value)
	{
		$this->attributes['geo_end'] = \DB::raw("ST_GeomFromText('POINT(" . implode(' ', $value) . ")')");
	}

	/**
	 * @return $this
	 */
	public function newQuery()
	{
		$raw = [];
		foreach ($this->geofields as $column) {
			$raw[] = " ST_AsText(" . $column . ") AS " . $column;
		}

		$q = parent::newQuery()->addSelect('*', \DB::raw(implode(', ', $raw)));

		return $q;
	}

}
