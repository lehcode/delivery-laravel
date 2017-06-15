<?php

namespace App\Models;

use App\Extensions\UuidTrait;
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
		AuditableTrait;

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
		'geo_end'
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
		'departure_date' => 'required',
		'expected_delivery_date' => 'required',
		'recipient_id' => 'required',
		'customer_id' => 'required',
		'shipment_id' => 'required',
		'trip_id' => 'required',
		'payment_id' => 'nullable',
	];

	/**
	 * @var array
	 */
	protected $geofields = ['geo_start', 'geo_end'];

	/**
	 * The storage format of the model's date columns.
	 *
	 * @var string
	 */
	public $dateFormat = 'Y-m-d H:i:s';

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

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function getGeoStartAttribute($value)
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
	 * @param $value
	 *
	 * @return string
	 */
	public function getGeoEndAttribute($value)
	{
		return $this->formatLocationAttribute($value);
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
	private function formatLocationAttribute($value)
	{
		$loc = explode(' ', preg_replace('/[^\d\s]+/', '', $value));

		foreach ($loc as $k => $coord) {
			$loc[$k] = $coord / 100000;
		}

		$loc = implode(',', $loc);

		return $loc;
	}

}
