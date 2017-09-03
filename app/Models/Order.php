<?php

namespace App\Models;

use App\Exceptions\MultipleExceptions;
use App\Extensions\UuidTrait;
use App\Extensions\RfcDateTrait;
use App\Models\User\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * Class Order
 * @package App\Models
 */
class Order extends Model implements AuditableInterface
{
	use UuidTrait,
		SoftDeletes,
		ValidatingTrait,
		AuditableTrait,
		RfcDateTrait;

	const STATUS_CREATED = "created";
	const STATUS_ACCEPTED = "accepted";
	const STATUS_AT_THE_DOOR = "at-the-door";
	const STATUS_PICKED = "picked";
	const STATUS_DELIVERING = "delivering";
	const STATUS_DELIVERED = "delivered";
	const STATUS_COMPLETED = "completed";
	const STATUS_CANCELLED = "cancelled";

	/**
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * @var array
	 */
	protected $fillable = [
		'id',
		'departure_date',
		'expected_delivery_date',
		'recipient_id',
		'customer_id',
		'shipment_id',
		'trip_id',
		'payment_id',
		'delivery_id',
		'geo_start',
		'geo_end',
		'status',
		'price',
	];

	/**
	 * @var array
	 */
	protected $dates = [
		'departure_date',
		'expected_delivery_date',
		'deleted_at',
		'created_at',
		'updated_at'
	];

	/**
	 * @var array
	 */
	protected $rules = [
		'departure_date' => 'required|date',
		'expected_delivery_date' => 'required|date',
		'recipient_id' => 'required|regex:' . User::UUID_REGEX,
		'customer_id' => 'required|regex:' . User::UUID_REGEX,
		'shipment_id' => 'required|regex:' . User::UUID_REGEX,
		'trip_id' => 'nullable|regex:' . User::UUID_REGEX,
		'payment_id' => 'nullable|regex:' . User::UUID_REGEX,
		'price' => 'numeric|min:1|max:100000',
	];

	/**
	 * @var array
	 */
	protected $geofields = ['geo_start', 'geo_end'];

	/**
	 * @var array
	 */
	protected $hidden = ['updated_at'];

	/**
	 * @var array
	 */
	protected $auditExclude = ['id', 'created_at', 'updated_at'];

	/**
	 * @var array
	 */
	protected $auditableEvents = ['deleted', 'updated', 'restored'];
	
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function trip()
	{
		return $this->belongsTo(Trip::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function recipient()
	{
		return $this->belongsTo(Recipient::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function shipment()
	{
		return $this->belongsTo(Shipment::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class);
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
		return $this->formatLocationAttribute($value);
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function getGeoEndAttribute($value)
	{
		return $this->formatLocationAttribute($value);
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

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function getDepartureDateAttribute($value)
	{
		return $this->rfcDate($value);
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function getExpectedDeliveryDateAttribute($value)
	{
		return $this->rfcDate($value);
	}

	/**
	 * @param string $value
	 *
	 * @return array
	 */
	private function formatLocationAttribute($value)
	{
		$ex = explode(' ', preg_replace('/[^\d\s\.]+/', '', $value));
		$ex[0] = (float)$ex[0];
		$ex[1] = (float)$ex[1];

		return $ex;
	}
}
