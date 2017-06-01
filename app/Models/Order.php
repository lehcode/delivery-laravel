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
	];

	/**
	 * @var array
	 */
	protected $dates = [
		'departure_date',
		'expected_delivery_date',
		'deleted_at',
		'created_at',
		'updated_at'];

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

}
