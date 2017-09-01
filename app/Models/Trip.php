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
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
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
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function fromCity()
	{
		return $this->hasOne(City::class, 'id', 'from_city_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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

}
