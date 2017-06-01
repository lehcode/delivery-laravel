<?php

namespace App\Models;

use App\Extensions\UuidTrait;
use App\Models\User\Carrier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Watson\Validating\ValidatingTrait;

/**
 * Class Trip
 * @package App\Models
 */
class Trip extends Model implements AuditableInterface
{

	use UuidTrait,
		SoftDeletes,
		ValidatingTrait,
		AuditableTrait;
	
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

	protected $visible = [
		'id',
		'payment_type_id',
		'carrier_id',
		'from_city_id',
		'to_city_id',
		'departure_date',
		'created_at',
		'updated_at',
	];

	/**
	 * @var array
	 */
	protected $rules = [
		'payment_type_id' => 'required',
		'carrier_id' => 'required',
		'from_city_id' => 'required',
		'to_city_id' => 'required',
		'departure_date' => 'required|date',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function carrier()
	{
		return $this->belongsTo(Carrier::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function paymentType()
	{
		return $this->belongsTo(PaymentType::class);
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
		return $this->belongsTo(City::class, 'from_city_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function destinationCity()
	{
		return $this->belongsTo(City::class, 'to_city_id');
	}

}
